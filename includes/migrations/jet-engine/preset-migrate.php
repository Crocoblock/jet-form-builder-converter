<?php


namespace Jet_Form_Builder_Converter\Migrations\Jet_Engine;


use Jet_Form_Builder\Presets\Preset_Manager;
use Jet_Form_Builder_Converter\Migrations\Base_Settings_Migrate;

class Preset_Migrate extends Base_Settings_Migrate {

	private function allowed_sources() {
		return array( 'query_var', 'post', 'user' );
	}

	protected function parse_value( $value ) {
		if ( empty( $value ) ) {
			return '';
		}

		if ( isset( $value['from'] ) && 'query_vars' === $value['from'] ) {
			$value['from'] = 'query_var';
		}
		if ( isset( $value['from'] ) && ! in_array( $value['from'], $this->allowed_sources() ) ) {
			$value['from'] = '';
		}

		$map = $value['fields_map'] ?? array();

		foreach ( $map as $name => $field ) {
			if ( 'login' === $field['prop'] ) {
				$value['fields_map'][ $name ]['prop'] = 'user_login';
			}
			if ( 'email' === $field['prop'] ) {
				$value['fields_map'][ $name ]['prop'] = 'user_email';
			}
		}

		return wp_json_encode( $value );
	}
}