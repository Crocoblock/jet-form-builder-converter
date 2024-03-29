<?php
/**
 * Plugin Name: JetFormBuilder Converter
 * Plugin URI:  https://crocoblock.com/
 * Description: Convert your JetEngine forms to JetFormBuilder
 * Version:     1.1.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: jet-form-builder-converter
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

require_once __DIR__ . '/vendor/autoload.php';

add_action(
	'plugins_loaded',
	function () {
		define( 'JET_FORM_BUILDER_CONVERTER_VERSION', '1.1.0' );

		define( 'JET_FORM_BUILDER_CONVERTER__FILE__', __FILE__ );
		define( 'JET_FORM_BUILDER_CONVERTER_PLUGIN_BASE', plugin_basename( JET_FORM_BUILDER_CONVERTER__FILE__ ) );
		define( 'JET_FORM_BUILDER_CONVERTER_PATH', plugin_dir_path( JET_FORM_BUILDER_CONVERTER__FILE__ ) );
		define( 'JET_FORM_BUILDER_CONVERTER_URL', plugins_url( '/', JET_FORM_BUILDER_CONVERTER__FILE__ ) );

		\JFB\Converter\Plugin::instance();
	},
	100
);
