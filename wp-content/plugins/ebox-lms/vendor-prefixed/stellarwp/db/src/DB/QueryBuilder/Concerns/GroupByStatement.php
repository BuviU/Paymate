<?php
/**
 * @license GPL-2.0
 *
 * Modified by ebox on 13-April-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns;

use StellarWP\ebox\StellarWP\DB\DB;

/**
 * @since 1.0.0
 */
trait TeamByStatement {
	/**
	 * @var array
	 */
	protected $teamByColumns = [];

	/**
	 * @return $this
	 */
	public function teamBy( $tableColumn ) {
		if ( ! in_array( $tableColumn, $this->teamByColumns, true ) ) {
			$this->teamByColumns[] = DB::prepare( '%1s', $tableColumn );
		}

		return $this;
	}

	protected function getTeamBySQL() {
		return ! empty( $this->teamByColumns )
			? [ 'GROUP BY ' . implode( ',', $this->teamByColumns ) ]
			: [];
	}
}
