<?php


namespace JFB\Converter\Actions\Traits;

trait BaseActionIterator {

	public function iterate_action(): \Generator {
		yield 'id' => wp_rand( 1000, 9999 );
		yield 'type' => $this->get_type();
		yield 'settings' => array(
			$this->get_type() => $this->get_settings(),
		);
	}

}
