<?php


namespace JFB\Converter\Compatibility\JetEngine\PostType;

use JFB\Converter\PostType\PostRowActionsBase;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class PostRowActions extends PostRowActionsBase {

	public function get_provider(): string {
		return 'jet_engine';
	}

	public function get_post_type(): string {
		return 'jet-engine-booking';
	}
}
