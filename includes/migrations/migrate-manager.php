<?php

namespace Jet_Form_Builder_Converter\Migrations;

use Jet_Form_Builder_Converter\Migrations\Types\Jet_Engine_Migrant;

class Migrate_Manager {

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 */
	public static $instance = null;

	private $_types = array();

	private function __construct() {
		$this->register_migrate_types();
		$this->register_hooks();
	}

	/**
	 * @return Base_Migrant[]
	 */
	protected function migrate_types(): array {
		return array(
			new Jet_Engine_Migrant()
		);
	}

	/**
	 * @return Base_Migrant[]
	 */
	public function get_migrate_types(): array {
		return $this->_types;
	}

	protected function register_migrate_types() {
		foreach ( $this->migrate_types() as $type ) {
			$this->_types[ $type->get_provider() ] = $type;
		}
	}

	private function register_hooks() {
		foreach ( $this->get_migrate_types() as $type ) {
			add_action(
				'admin_action_migrate_' . $type->get_provider(),
				array( $type, 'run_migrate' )
			);
		}
	}

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @return Migrate_Manager An instance of the class.
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}