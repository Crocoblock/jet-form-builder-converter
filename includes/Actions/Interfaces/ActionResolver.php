<?php


namespace JFB\Converter\Actions\Interfaces;

interface ActionResolver {

	public function is_supported( array $action ): bool;

	public function resolve_type( array $action );

	public function set_type( string $type );

	public function get_type(): string;

	public function resolve_settings( array $action );

	public function set_settings( array $settings );

	public function get_settings(): array;

	public function iterate_action(): \Generator;

}
