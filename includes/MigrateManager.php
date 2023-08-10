<?php

namespace JFB\Converter;

use JFB\Converter\Migrator\BaseMigrant;
use JFB\Converter\Compatibility\JetEngine;

class MigrateManager {

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 * @var self
	 */
	public static $instance = null;

	private $types = array();

	private function __construct() {
		$this->register_migrate_types();
		$this->register_hooks();
	}

	/**
	 * @return BaseMigrant[]
	 */
	protected function migrate_types(): array {
		return array(
			new JetEngine\Migrant(),
		);
	}

	/**
	 * @return BaseMigrant[]
	 */
	public function get_migrate_types(): array {
		return $this->types;
	}

	protected function register_migrate_types() {
		foreach ( $this->migrate_types() as $type ) {
			$this->types[ $type->get_provider() ] = $type;
		}
	}

	private function register_hooks() {
		foreach ( $this->get_migrate_types() as $type ) {
			$type->init_hooks();
		}
	}

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @return MigrateManager An instance of the class.
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}
