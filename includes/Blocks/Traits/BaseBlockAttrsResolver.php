<?php


namespace JFB\Converter\Blocks\Traits;

trait BaseBlockAttrsResolver {

	private $block_attrs = array();

	public function set_attrs( array $attrs ) {
		$this->block_attrs = $attrs;
	}

	public function get_attrs(): array {
		return $this->block_attrs;
	}

	public function has_attr( string $name ): bool {
		return array_key_exists( $name, $this->block_attrs );
	}

	public function get_attr( string $name ) {
		return $this->block_attrs[ $name ] ?? false;
	}

	public function set_attr( string $name, $value ) {
		$this->block_attrs[ $name ] = $value;
	}

	public function delete_attr( string $name ) {
		unset( $this->block_attrs[ $name ] );
	}

}
