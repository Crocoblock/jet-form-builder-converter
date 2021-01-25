<?php

namespace Jet_Form_Builder_Converter;

// If this file is called directly, abort.
use Jet_Form_Builder_Converter\Migrations\Migrate_Manager;

if ( ! defined( 'WPINC' ) ) {
    die();
}

class Plugin
{
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

    private $can_init = false;

    public function __construct() {
        $this->can_init();
        $this->init();
    }

    public function can_init() {
        $this->can_init = function_exists( 'jet_engine' ) && function_exists( 'jet_form_builder' );
    }

    public function init() {
        if ( ! $this->can_init ) return;

        $this->register_autoloader();
        $this->hooks();
    }

    public function hooks() {
        add_action(
            'after_setup_theme',
            array( $this, 'init_components' )
        );
    }

    public function init_components() {
    	new Post_Row_Actions();
		Migrate_Manager::instance();
    }

    /**
     * Register autoloader.
     */
    public function register_autoloader() {
        require JET_FORM_BUILDER_CONVERTER_PATH . 'includes/autoloader.php';
        Autoloader::run();
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

Plugin::instance();