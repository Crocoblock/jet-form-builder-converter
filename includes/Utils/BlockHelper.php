<?php


namespace JFB\Converter\Utils;

use Jet_Form_Builder\Blocks\Types\Base;
use JFB\Converter\Blocks\Interfaces\BlockResolver;

class BlockHelper {

	public static function get_attrs( Base $block ): array {
		if ( method_exists( $block, 'block_attributes' ) ) {
			return $block->block_attributes( false );
		}

		return $block->get_attributes();
	}

	public static function sanitize_attrs( BlockResolver $resolver ) {
		$attrs      = $resolver->get_attrs();
		$block_type = \WP_Block_Type_Registry::get_instance()->get_registered( $resolver->get_block_type() );

		foreach ( $attrs as $name => $value ) {
			if ( ! array_key_exists( $name, $block_type->attributes ) ) {
				$resolver->delete_attr( $name );
				continue;
			}

			switch ( $block_type->attributes[ $name ]['type'] ) {
				case 'number':
					$resolver->set_attr( $name, (float) $value );
					break;
				case 'boolean':
					$resolver->set_attr( $name, (bool) $value );
					break;
			}
		}
	}

}
