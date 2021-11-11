<?php


namespace Jet_Form_Builder_Converter\Migrations\Jet_Engine;



class Settings_Manager {

	private $form_meta;

	public function __construct( $form_meta = array() ) {
		$this->form_meta = $this->save_settings( $form_meta );
	}

	public function get_all() {
		return $this->form_meta;
	}

	public function parse_settings() {
		foreach ( $this->form_meta as $key => $meta_data ) {
			$this->form_meta[ $key ] = $this->parse_setting( $meta_data );
		}

		return $this;
	}

	private function parse_setting( $meta_data ) {
		if ( ! is_array( $meta_data ) ) {
			return $meta_data;
		}

		if ( empty( $meta_data['transformer'] ) ) {
			return $meta_data;
		}

		switch ( $meta_data['transformer'] ) {
			case 'actions':
				return ( new Actions_Migrate( $meta_data['value'] ) )->value();
			case 'preset':
				return ( new Preset_Migrate( $meta_data['value'] ) )->value();
		}
	}

	private function save_settings( $form_meta ) {
		$preset   = maybe_unserialize( $form_meta['_preset'][0] );
		$messages = wp_json_encode( maybe_unserialize( $form_meta['_messages'][0] ) );
		$captcha  = wp_json_encode( maybe_unserialize( $form_meta['_captcha'][0] ) );
		$actions  = json_decode( wp_unslash( $form_meta['_notifications_data'][0] ), true );

		return array(
			'_jf_preset'    => array(
				'value'       => $preset,
				'transformer' => 'preset'
			),
			'_jf_actions'   => array(
				'value'       => $actions,
				'transformer' => 'actions'
			),
			'_jf_messages'  => $messages,
			'_jf_recaptcha' => $captcha,
		);

	}

}