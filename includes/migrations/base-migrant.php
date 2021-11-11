<?php

namespace Jet_Form_Builder_Converter\Migrations;

abstract class Base_Migrant {

	protected $form_id;
	protected $form_data;
	protected $form_meta_data;

	protected $fields = array();
	protected $settings = array();
	protected $transformed_fields = array();
	protected $transformed_settings = array();

	abstract public function get_provider();

	abstract public function source_fields();

	abstract public function source_settings();

	abstract public function migrate_form();

	public function set_form_id( $form_id ) {
		$this->form_id = $form_id;
		$this->_set_form_data();
		$this->_set_source_fields();
		$this->_set_source_settings();
	}

	public function _set_form_data() {
		$this->form_data      = $this->source_form_data();
		$this->form_meta_data = $this->source_form_meta_data();
	}

	public function _set_source_fields() {
		$this->fields = $this->source_fields();
	}


	public function _set_source_settings() {
		$this->settings = $this->source_settings();
	}

	public function source_form_data() {
		return get_post( $this->form_id );
	}

	public function source_form_meta_data() {
		return get_post_meta( $this->form_id );
	}

	public function run_migrate() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$form_id = absint( $_GET['id'] ?? 0 );

		if ( ! $form_id ) {
			return;
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
