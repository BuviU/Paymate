<?php
/**
 * @license GPL-2.0
 *
 * Modified by ebox on 13-April-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns;

/**
 * @since 1.0.0
 */
trait OffsetStatement {
	/**
	 * @var int
	 */
	protected $offset;

	/**
	 * @param  int  $offset
	 *
	 * @return $this
	 */
	public function offset( $offset ) {
		$this->offset = (int) $offset;

		return $this;
	}

	protected function getOffsetSQL() {
		return $this->limit && $this->offset
			? [ "OFFSET {$this->offset}" ]
			: [];
	}
}
