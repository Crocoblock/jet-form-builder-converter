<?php


namespace Jet_Form_Builder_Converter;


use Jet_Form_Builder\Blocks\Types\Base;
use Jet_Form_Builder_Converter\Migrations\Jet_Engine\Preset_Migrate;

class Jet_Field_Parser {

	const CUSTOM_ATTR_PARSER_PREFIX = 'parse_attribute__';
	private $block;
	private $field;

	private function callable_fields_parser() {
		return array(
			'hidden-field' => array( $this, 'parse__hidden_field' )
		);
	}

	public function __construct( Base $block, array $field_data ) {
		$this->block = $block;
		$this->field = $field_data;
	}

	public function parse_exported_data() {
		$field_attrs = Utils::get_attrs( $this->block );

		foreach ( $this->field['attrs'] as $attribute => $value ) {
			if ( ! isset( $field_attrs[ $attribute ] ) ) {
				continue;
			}
			$func_name = self::CUSTOM_ATTR_PARSER_PREFIX . $attribute;

			if ( is_callable( array( $this, $func_name ) ) ) {
				$this->field['attrs'][ $attribute ] = call_user_func( array( $this, $func_name ), $value );
				continue;
			}

			switch ( $field_attrs[ $attribute ]['type'] ) {
				case 'number':
					$this->field['attrs'][ $attribute ] = (float) $value;
					break;
				case 'boolean':
					$this->field['attrs'][ $attribute ] = (boolean) $value;
			}
		}

		return $this->field;
	}

	public function parse_attribute__default( $value ) {
		$dynamic_value = json_decode( $value, true );

		return $dynamic_value ? ( new Preset_Migrate( $dynamic_value ) )->value() : $value;
	}

	public function parse__hidden_field() {
		list(
			$this->field['attrs']['field_value'],
			$this->field['attrs']['hidden_value']
			) = [
			$this->field['attrs']['hidden_value'],
			$this->field['attrs']['default']
		];

	}

	public function response() {
		$this->parse_exported_data();

		$this->maybe_call_field_parser( $this->block->get_name() );

		return $this->field;
	}

	private function maybe_call_field_parser( $slug = false ) {
		if ( ! $slug ) {
			return;
		}
		$fields = $this->callable_fields_parser();

		if ( isset( $fields[ $slug ] ) && is_callable( $fields[ $slug ] ) ) {
			call_user_func( $fields[ $slug ] );
		}
	}


}