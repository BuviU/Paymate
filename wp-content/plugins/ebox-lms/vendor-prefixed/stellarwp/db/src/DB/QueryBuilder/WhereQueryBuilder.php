<?php
/**
 * @license GPL-2.0
 *
 * Modified by ebox on 13-April-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\ebox\StellarWP\DB\QueryBuilder;

use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\WhereClause;

/**
 * @since 1.0.0
 */
class WhereQueryBuilder {
	use WhereClause;

	/**
	 * @return string[]
	 */
	public function getSQL() {
		return $this->getWhereSQL();
	}
}
