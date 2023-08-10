<?php

namespace JFB\Converter;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Plugin {

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	public function __construct() {
		if ( ! function_exists( 'jet_engine' ) ||
			! function_exists( 'jet_form_builder' )
		) {
			return;
		}

		$this->hooks();
	}

	public function hooks() {
		add_action(
			'after_setup_theme',
			array( $this, 'init_components' )
		);
	}

	public function init_components() {
		MigrateManager::instance();
	}


	public function get_template_path( $template ) {
		$path = JET_FORM_BUILDER_CONVERTER_PATH . 'templates' . DIRECTORY_SEPARATOR;
		return ( $path . $template . '.php' );
	}

	public function get_version() {
		return JET_FORM_BUILDER_CONVERTER_VERSION;
	}

	public function plugin_url( $path ) {
		return JET_FORM_BUILDER_CONVERTER_URL . $path;
	}


	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}
