<?php


namespace Jet_Form_Builder_Converter;


use Jet_Form_Builder\Blocks\Types\Base;

class Utils {

	public static function get_attrs( Base $block ): array {
		if ( method_exists( $block,'block_attributes' ) ) {
			return $block->block_attributes( false );
		}

		return $block->get_attributes();
	}

}