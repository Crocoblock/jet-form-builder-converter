<?php


namespace JFB\Converter\Compatibility\JetEngine\Actions\Traits;

use JFB\Converter\Actions\Traits\BaseActionTypeResolver;

trait DefaultActionTypeResolver {

	use BaseActionTypeResolver;

	public function resolve_type( array $action ) {
		$this->set_type( $action['type'] ?? '' );
	}

}
