<?php

namespace JFB\Converter\Compatibility\JetEngine\Blocks;

use JFB\Converter\Interfaces\AfterResolveHook;
use JFB\Converter\Blocks\Interfaces\BlockResolver;
use JFB\Converter\Blocks\Traits\BaseBlockIterator;
use JFB\Converter\Compatibility\JetEngine\Blocks\Traits\DefaultBlockTypeResolver;
use JFB\Converter\Compatibility\JetEngine\Blocks\Traits\DefaultBlockAttrsResolver;
use JFB\Converter\Utils\BlockHelper;
use JFB\Converter\Compatibility\JetEngine;

class CheckInOutResolver implements BlockResolver, AfterResolveHook {

	use DefaultBlockTypeResolver;
	use DefaultBlockAttrsResolver;
	use BaseBlockIterator;

	public function is_supported( array $field ): bool {
		return 'check_in_out' === ( $field['settings']['type'] ?? '' );
	}

	public function after_resolve() {
		$this->block_type = str_replace(
			'check_in_out',
			'check-in-out',
			$this->get_block_type()
		);

		BlockHelper::sanitize_attrs( $this );
		JetEngine\Helpers\BlockHelper::sanitize_default( $this );
	}
}
