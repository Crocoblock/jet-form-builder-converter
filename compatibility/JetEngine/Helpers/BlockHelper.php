<?php


namespace JFB\Converter\Compatibility\JetEngine\Helpers;

use JFB\Converter\Blocks\Interfaces\BlockResolver;
use JFB\Converter\Compatibility\JetEngine\Migrations\PresetMigrate;

class BlockHelper {

	public static function sanitize_default( BlockResolver $resolver ) {
		if ( ! $resolver->has_attr( 'default' ) ) {
			return;
		}

		$value = $resolver->get_attr( 'default' );

		if ( ! is_string( $value ) ) {
			return;
		}

		$dynamic_value = json_decode( $value, true );

		if ( ! $dynamic_value ) {
			return;
		}

		$resolver->set_attr( 'default', ( new PresetMigrate( $dynamic_value ) )->value() );
	}

}
