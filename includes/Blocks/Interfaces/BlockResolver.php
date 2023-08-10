<?php


namespace JFB\Converter\Blocks\Interfaces;

interface BlockResolver {

	public function is_supported( array $field ): bool;

	public function resolve_block_type( array $field );

	public function resolve_attrs( array $field );

	public function set_block_type( string $name );

	public function get_block_type(): string;

	public function set_attrs( array $attrs );

	public function get_attrs(): array;

	public function has_attr( string $name ): bool;

	public function delete_attr( string $name );

	public function get_attr( string $name );

	public function set_attr( string $name, $value );

	public function iterate_block(): \Generator;

}
