<?php


namespace JFB\Converter\Blocks\Traits;

/**
 * Trait DefaultBlockIterator
 *
 * @package JFB\Converter\Blocks\Traits
 */
trait BaseBlockIterator {

	public function iterate_block(): \Generator {
		yield 'blockName' => $this->get_block_type();
		yield 'attrs' => $this->get_attrs();
	}

}
