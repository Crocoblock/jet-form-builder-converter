<?php


namespace JFB\Converter\Blocks\Interfaces;

interface BlockSupportedInnerBlocks {

	/**
	 * @param BlockResolver[] $blocks
	 *
	 * @return mixed
	 */
	public function set_inner_blocks( array $blocks );

	public function get_inner_blocks(): array;

}
