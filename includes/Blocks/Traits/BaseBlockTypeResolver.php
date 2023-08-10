<?php


namespace JFB\Converter\Blocks\Traits;

trait BaseBlockTypeResolver {

	private $block_type;

	public function set_block_type( string $name ) {
		$this->block_type = $name;
	}

	public function get_block_type(): string {
		return $this->block_type;
	}

}
