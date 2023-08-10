<?php


namespace JFB\Converter\Actions\Traits;

trait BaseActionTypeResolver {

	private $type = '';

	public function set_type( string $name ) {
		$this->type = $name;
	}

	public function get_type(): string {
		return $this->type;
	}

}
