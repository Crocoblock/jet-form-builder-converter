<?php

namespace JFB\Converter\Compatibility\JetEngine;

use JFB\Converter\Interfaces\AfterResolveHook;
use JFB\Converter\Blocks\Interfaces\BlockResolver;
use JFB\Converter\Compatibility\JetEngine\Blocks\AppointmentDateResolver;
use JFB\Converter\Compatibility\JetEngine\Blocks\AppointmentProviderResolver;
use JFB\Converter\Compatibility\JetEngine\Blocks\CheckInOutResolver;
use JFB\Converter\Compatibility\JetEngine\PostType\PostRowActions;
use JFB\Converter\Compatibility\JetEngine\Migrations\SettingsManager;
use JFB\Converter\Utils\BlockGenerator;
use JFB\Converter\Compatibility\JetEngine\Helpers\FieldParser;
use Jet_Form_Builder\Classes\Tools;
use Jet_Form_Builder\Plugin;
use JFB\Converter\Migrator\BaseMigrant;
use JFB\Converter\Utils\BlockHelper;

class Migrant extends BaseMigrant {

	const BLOCKS_NAMESPACE = 'jet-forms/';

	private $prepared_fields = array();
	private $migrate_fields  = array(
		'media'          => 'media-field',
		'hidden'         => 'hidden-field',
		'repeater_start' => 'repeater-field',
		'range'          => 'range-field',
		'submit'         => 'submit-field',
		'text'           => 'text-field',
		'wysiwyg'        => 'wysiwyg-field',
		'time'           => 'time-field',
		'date'           => 'date-field',
		'datetime-local' => 'datetime-field',
		'number'         => 'number-field',
		'select'         => 'select-field',
		'checkboxes'     => 'checkbox-field',
		'calculated'     => 'calculated-field',
		'radio'          => 'radio-field',
		'page_break'     => 'form-break-field',
		'textarea'       => 'textarea-field',
		'heading'        => 'heading-field',
		'group_break'    => 'group-break-field',
	);

	private $raw_fields;

	/** @var PostRowActions */
	private $post_actions;

	/** @var BlockResolver[] */
	private $resolvers;

	public function __construct() {
		$this->post_actions = new PostRowActions();

		$this->resolvers = array(
			new AppointmentDateResolver(),
			new AppointmentProviderResolver(),
			new CheckInOutResolver(),
		);
	}

	public function init_hooks() {
		parent::init_hooks();

		$this->post_actions->init_hooks();
	}

	public function get_provider() {
		return 'jet_engine';
	}


	public function source_settings() {
		$settings = new SettingsManager( $this->form_meta_data );

		return $settings->parse_settings()->get_all();
	}

	public function migrate_form() {
		$title = sprintf( '%1$s [%2$s]', $this->form_data->post_title, current_time( 'd/m/Y H:i' ) );

		return wp_insert_post(
			wp_slash(
				array(
					'post_status'  => $this->form_data->post_status,
					'post_type'    => Plugin::instance()->post_type->slug(),
					'post_title'   => $title,
					'post_author'  => $this->form_data->post_author,
					'post_content' => $this->fields,
					'meta_input'   => $this->settings,
				)
			)
		);
	}

	public function source_fields() {
		$data = json_decode( wp_unslash( $this->form_meta_data['_form_data'][0] ), true );
		unset( $this->form_meta_data['_form_data'] );

		$this->raw_fields = $data;

		usort(
			$this->raw_fields,
			function ( $first, $second ) {
				return $first['y'] - $second['y'];
			}
		);

		$column = false;

		foreach ( $this->raw_fields as $index => $current ) {
			if ( ! isset( $this->raw_fields[ $index + 1 ] ) ) {
				continue;
			}
			$next             = $this->raw_fields[ $index + 1 ];
			$not_child_column = $current['y'] !== $next['y'];

			if ( $not_child_column ) {
				$this->raw_fields[ $index ]['single_column_width'] = $this->calc_field_width_degrees( $current );
				$column = false;
				continue;
			} elseif ( ! $column ) {
				$column = $current['column_order'] ?? $index;
			}

			$current['column_width'] = $this->calc_field_width_degrees( $current );
			$next['column_width']    = $this->calc_field_width_degrees( $next );

			$this->add_field_in_column( $column, $current );
			$this->add_field_in_column( $column, $next );

			unset(
				$this->raw_fields[ $index + 1 ]
			);
		}

		foreach ( $this->raw_fields as $index => $field ) {
			if ( ! isset( $field['innerBlocks'] ) || empty( $field['innerBlocks'] ) ) {
				continue;
			}
			krsort( $field['innerBlocks'], SORT_NUMERIC );

			foreach ( $field['innerBlocks'] as $position => $inner_column ) {
				$next = next( $field['innerBlocks'] );

				$column_width = $this->get_between_column_width( $position, $next );

				if ( 0 !== $column_width ) {
					$column_position = $this->get_field_right_border_position( $next ) + $column_width / 2.00;
					$column_width    = $this->calc_width_degrees( $column_width );

					$field['innerBlocks'][ $column_position ] = $this->_get_child_column( $column_width );
				}
			}

			ksort( $field['innerBlocks'], SORT_NUMERIC );
			$this->raw_fields[ $index ] = $field;
		}

		$this->get_prepare_fields( $this->raw_fields );

		return ( new BlockGenerator( $this->prepared_fields ) )->generate();
	}

	private function get_between_column_width( $position, $next ) {
		return $next ? ( $position - $this->get_field_right_border_position( $next ) ) : 0;
	}

	private function get_field_right_border_position( $field ) {
		return $field['columns'] + $field['position'];
	}


	private function calc_field_width_degrees( $field ) {
		return $field['column_width'] ?? $this->calc_width_degrees( $field['w'] );
	}

	private function calc_width_degrees( $count_columns ) {
		return number_format( ( $count_columns * 100 ) / 12, 2 );
	}

	private function get_prepare_fields( $fields ) {
		$inner = false;
		foreach ( $fields as $index => $field ) {
			if ( ! $inner && isset( $fields[ $index - 1 ] ) ) {
				$prev = $this->raw_fields[ $index - 1 ];

				$inner = 'repeater_start' === $prev['settings']['type'] ? $prev['settings']['name'] : false;

			} elseif ( 'repeater_end' === $field['settings']['type'] ) {
				$inner = false;
				continue;
			}

			$this->prepare_field( $field, $inner );
		}
	}

	private function add_field_in_column( $column_id, $field ) {
		if ( ! isset( $this->raw_fields[ $column_id ]['innerBlocks'] ) ) {
			$this->raw_fields[ $column_id ] = $this->_get_columns();
		}

		if ( ! isset( $this->raw_fields[ $column_id ]['innerBlocks'][ $current['x'] ] ) ) {

			$field_data = $this->get_prepare_field( $field );
			$field_data = $this->maybe_add_conditional( $field, $field_data );
			$field_data = $this->maybe_add_in_column( $field, $field_data );

			$this->raw_fields[ $column_id ]['innerBlocks'][ $field['x'] ] = $this->_get_child_column(
				$field['column_width'],
				array( $field_data ),
				array(
					'columns'  => $field['w'],
					'position' => $field['x'],
				)
			);
		}
	}

	private function _get_columns( $inner_columns = array() ) {
		$response = array(
			'blockName'    => 'columns',
			'innerContent' => array(
				'<div class="wp-block-columns">',
				'</div>',
			),
		);

		return $inner_columns ? array_merge( $response, array( 'innerBlocks' => $inner_columns ) ) : $response;
	}

	private function _get_child_column( $width, $inner_blocks = array(), $additional_data = array() ) {
		return array_merge(
			array(
				'attrs'        => array(
					'width' => $width . '%',
				),
				'blockName'    => 'column',
				'innerBlocks'  => $inner_blocks,
				'innerContent' => array(
					'<div class="wp-block-column" style="flex-basis:' . $width . '%">',
					'</div>',
				),
			),
			$additional_data
		);
	}

	private function maybe_add_in_column( $field, $field_data ) {
		if ( ! isset( $field['single_column_width'] ) || 100 === (int) $field['single_column_width'] ) {
			return $field_data;
		}

		$columns = array();
		if ( 0 !== $field['x'] ) {
			$columns[] = $this->_get_child_column( $this->calc_width_degrees( $field['x'] ) );
		}

		$columns[] = $this->_get_child_column( $field['single_column_width'], array( $field_data ) );

		return $this->_get_columns( $columns );
	}

	public function prepare_field( $current, $inner = false ) {
		if ( isset( $current['innerBlocks'] ) && isset( $current['blockName'] ) ) {
			$this->save_prepare_field( $current, $inner );

			return;
		}

		$attrs      = $current['settings'];
		$field_type = $this->get_field_type( $attrs );
		$field_data = $this->get_prepare_field( $current, $field_type );

		if ( is_null( $field_data ) ) {
			return;
		}

		$field_data = $this->maybe_add_conditional( $current, $field_data );
		$field_data = $this->maybe_add_in_column( $current, $field_data );

		$this->save_prepare_field( $field_data, $inner, $attrs, $field_type );
	}

	private function save_prepare_field( $field_data, $inner, $attrs = array(), $field_type = null ) {
		if ( $inner ) {
			$this->prepared_fields[ $inner ]['innerBlocks'][] = $field_data;
		} elseif ( 'repeater-field' === $field_type ) {
			$this->prepared_fields[ $attrs['name'] ] = $field_data;
		} else {
			$this->prepared_fields[] = $field_data;
		}
	}

	public function get_prepare_field( $current, $field_type = false ) {
		foreach ( $this->resolvers as $resolver ) {
			if ( ! $resolver->is_supported( $current ) ) {
				continue;
			}
			$resolver = clone $resolver;
			$resolver->resolve_block_type( $current );
			$resolver->resolve_attrs( $current );

			if ( $resolver instanceof AfterResolveHook ) {
				$resolver->after_resolve();
			}

			return iterator_to_array( $resolver->iterate_block() );
		}
		$attrs = $current['settings'];

		if ( isset( $current['innerBlocks'] ) && ! empty( $current['innerBlocks'] ) ) {
			$this->prepared_fields[] = $current;

			return;
		} elseif ( ! $this->isset_field_type( $attrs ) ) {
			return;
		}
		if ( ! $field_type ) {
			$field_type = $this->get_field_type( $attrs );
		}

		$field_object = Plugin::instance()->blocks->get_field_by_name( $field_type );

		if ( ! $field_object ) {
			return;
		}

		$field_data = array(
			'attrs'     => Tools::array_merge_intersect_key( BlockHelper::get_attrs( $field_object ), $attrs ),
			'blockName' => self::BLOCKS_NAMESPACE . $field_type,
		);

		return ( new FieldParser( $field_object, $field_data ) )->response();
	}

	private function maybe_add_conditional( $current, $field_data ) {
		if ( ! isset( $current['conditionals'] ) || empty( $current['conditionals'] ) ) {
			return $field_data;
		}

		$response                  = array(
			'attrs'     => array( 'conditions' => $current['conditionals'] ),
			'blockName' => self::BLOCKS_NAMESPACE . 'conditional-block',
		);
		$response['innerBlocks'][] = $field_data;

		return $response;
	}

	private function isset_field_type( $field ) {
		return isset( $this->migrate_fields[ $field['type'] ] );
	}

	private function get_field_type( $field ) {
		return $this->migrate_fields[ $field['type'] ?? '' ] ?? '';
	}

	/**
	 * @return PostRowActions
	 */
	public function get_post_actions(): PostRowActions {
		return $this->post_actions;
	}
}
