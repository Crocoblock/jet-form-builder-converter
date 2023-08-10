<?php

namespace JFB\Converter\Migrator;

abstract class BaseMigrant {

	protected $form_id;
	protected $form_data;
	protected $form_meta_data;

	protected $fields               = array();
	protected $settings             = array();
	protected $transformed_fields   = array();
	protected $transformed_settings = array();

	abstract public function get_provider();

	abstract public function source_fields();

	abstract public function source_settings();

	abstract public function migrate_form();

	public function init_hooks() {
		add_action(
			'admin_action_migrate_' . $this->get_provider(),
			array( $this, 'run_migrate' )
		);
	}

	public function set_form_id( $form_id ) {
		$this->form_id = $form_id;

		$this->form_data      = get_post( $this->form_id );
		$this->form_meta_data = get_post_meta( $this->form_id );
		$this->fields         = $this->source_fields();
		$this->settings       = $this->source_settings();
	}

	public function run_migrate() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$form_id = absint( $_GET['id'] ?? 0 );

		if ( ! $form_id ) {
			return;
		}

		if ( ! current_user_can( 'publish_jet_fb_forms' ) ) {
			wp_die(
				esc_html__(
					'You do not have permission to create a new form (post type jet-form-builder)',
					'jet-form-builder-converter'
				)
			);
		}

		$this->set_form_id( $form_id );

		$migrated_form_id = $this->migrate_form();

		$this->redirect_to( $migrated_form_id );
	}

	public function redirect_to( $post_id ) {
		if ( $post_id && ! $post_id instanceof \WP_Error ) {
			wp_safe_redirect( get_edit_post_link( $post_id, '' ) );
		}
		die();
	}
}
