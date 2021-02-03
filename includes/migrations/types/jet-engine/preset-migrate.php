<?php


namespace Jet_Form_Builder_Converter\Migrations\Types\Jet_Engine;


use Jet_Form_Builder\Presets\Preset_Manager;
use Jet_Form_Builder_Converter\Migrations\Base_Settings_Migrate;

class Preset_Migrate extends Base_Settings_Migrate {

	private function allowed_sources() {
		return array( 'query_var', 'post', 'user' );
	}

	protected function parse_value( $value ) {

		if ( $value['from'] && 'query_vars' === $value['from'] ) {
			$value['from'] = 'query_var';
		}
		if ( ! in_array( $value['from'], $this->allowed_sources() ) ) {
			$value['from'] = '';
		}

		foreach ( $value['fields_map'] as $name => $field ) {
			if ( 'login' === $field['prop'] ) {
				$value['fields_map'][ $name ]['prop'] = 'user_login';
			}
			if ( 'email' === $field['prop'] ) {
				$value['fields_map'][ $name ]['prop'] = 'user_email';
			}
		}

		return json_encode( $value );
	}
}