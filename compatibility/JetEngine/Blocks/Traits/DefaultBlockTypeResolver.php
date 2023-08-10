<?php


namespace JFB\Converter\Compatibility\JetEngine\Blocks\Traits;

use JFB\Converter\Blocks\Traits\BaseBlockTypeResolver;

trait DefaultBlockTypeResolver {

	use BaseBlockTypeResolver;

	public function resolve_block_type( array $field ) {
		$this->set_block_type( sprintf( 'jet-forms/%s', $field['settings']['type'] ) );
	}

}
