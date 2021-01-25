<?php

namespace Jet_Form_Builder_Converter\Migrations;

use Jet_Form_Builder\Classes\Factory;

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

	private function __construct() {
		add_action( 
			'admin_action_' . $this->action_name(),
			array( $this, $this->action_name() )
		);
	}

	public function action_name() {
		return 'migrate_jet_engine';
	}

	public function migrate_jet_engine() {
		$this->run_migrate( 'jet-engine' );
	}

	public function run_migrate( $type ) {
		if ( ! isset( $_REQUEST['id'] ) || empty( $_REQUEST['id'] ) ) {
			return;
		}
		$form_id = absint( $_REQUEST['id'] );

		$migrant = ( new Factory( 'Jet_Form_Builder_Converter\\Migrations\\Types\\' ) )
			->suffix( '\\Migrant' )
			->create_one( $type, $form_id );
		
		if ( ! $migrant instanceof Base_Migrant ) {
			return;
		}

		$form_id = $migrant->migrate_form();

		if ( $form_id && ! $form_id instanceof \WP_Error ) {
			wp_redirect( get_edit_post_link( $form_id, '' ) );
			die();
		}
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
	 * @return Migrate_Manager An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}