<?php


namespace JFB\Converter\Compatibility\JetEngine\Migrations;

use Jet_Form_Builder\Classes\Tools;
use JFB\Converter\Actions\Interfaces\ActionResolver;
use JFB\Converter\Compatibility\JetEngine\Actions\ApartmentBookingAction;
use JFB\Converter\Compatibility\JetEngine\Actions\InsertAppointmentAction;
use JFB\Converter\Interfaces\AfterResolveHook;
use JFB\Converter\Migrator\BaseSettingsMigrate;
use Jet_Form_Builder\Plugin;

class ActionsMigrate extends BaseSettingsMigrate {

	/** @var ActionResolver[] */
	private $resolvers;

	public function __construct( $value ) {
		$this->resolvers = array(
			new InsertAppointmentAction(),
			new ApartmentBookingAction(),
		);

		parent::__construct( $value );
	}

	public $transform_compatibility = array(
		'email'          => 'send_email',
		'activecampaign' => 'active_campaign',
		'webhook'        => 'call_webhook',
		'hook'           => 'call_hook',
		'redirect'       => 'redirect_to_page',
	);

	protected function parse_value( $value ) {
		$prepared_actions = array();

		foreach ( $value as $index => $action ) {
			$resolver = $this->get_resolver( $action );

			if ( $resolver instanceof ActionResolver ) {
				$resolver->resolve_type( $action );
				$resolver->resolve_settings( $action );

				if ( $resolver instanceof AfterResolveHook ) {
					$resolver->after_resolve();
				}

				$prepared_actions[] = iterator_to_array( $resolver->iterate_action() );
				continue;
			}

			$type = $this->get_action_type( $action );

			if ( ! $type ) {
				continue;
			}

			$prepared_actions[] = array(
				'id'       => wp_rand( 1000, 9999 ),
				'type'     => $type,
				'settings' => array(
					$type => Tools::array_merge_intersect_key(
						$this->get_action_attributes( $type ),
						$action
					),
				),
			);
		}

		return wp_json_encode( $prepared_actions );
	}

	public function get_action_attributes( $type ): array {
		$manager = Plugin::instance()->actions;
		$action  = $manager->get_actions( $type );

		if ( method_exists( $action, 'action_attributes' ) ) {
			return $action->action_attributes();
		}

		return array();
	}

	public function get_action_type( $action ) {
		$manager = Plugin::instance()->actions;

		if ( $manager->has_action_type( $action['type'] ) ) {
			return $action['type'];
		} elseif ( isset( $this->transform_compatibility[ $action['type'] ] ) ) {
			return $this->transform_compatibility[ $action['type'] ];
		}

		return false;
	}

	/**
	 * @param array $action
	 *
	 * @return false|ActionResolver
	 */
	private function get_resolver( array $action ) {
		foreach ( $this->resolvers as $resolver ) {
			if ( ! $resolver->is_supported( $action ) ) {
				continue;
			}

			return clone $resolver;
		}

		return false;
	}
}
