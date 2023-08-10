<?php


namespace JFB\Converter\Compatibility\JetEngine\Blocks\Traits;

use JFB\Converter\Blocks\Traits\BaseBlockAttrsResolver;

trait DefaultBlockAttrsResolver {

	use BaseBlockAttrsResolver;

	public function resolve_attrs( array $field ) {
		$this->set_attrs( $field['settings'] );
	}

}
