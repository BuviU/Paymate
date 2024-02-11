<?php
/**
 * @license GPL-2.0
 *
 * Modified by ebox on 13-April-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\ebox\StellarWP\DB\QueryBuilder;

use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\Aggregate;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\CRUD;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\FromClause;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\TeamByStatement;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\HavingClause;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\JoinClause;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\LimitStatement;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\MetaQuery;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\OffsetStatement;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\OrderByStatement;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\SelectStatement;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\TablePrefix;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\UnionOperator;
use StellarWP\ebox\StellarWP\DB\QueryBuilder\Concerns\WhereClause;

/**
 * @since 1.0.0
 */
class QueryBuilder {
	use Aggregate;
	use CRUD;
	use FromClause;
	use TeamByStatement;
	use HavingClause;
	use JoinClause;
	use LimitStatement;
	use MetaQuery;
	use OffsetStatement;
	use OrderByStatement;
	use SelectStatement;
	use TablePrefix;
	use UnionOperator;
	use WhereClause;

	/**
	 * @return string
	 */
	public function getSQL() {
		$sql = array_merge(
			$this->getSelectSQL(),
			$this->getFromSQL(),
			$this->getJoinSQL(),
			$this->getWhereSQL(),
			$this->getTeamBySQL(),
			$this->getHavingSQL(),
			$this->getOrderBySQL(),
			$this->getLimitSQL(),
			$this->getOffsetSQL(),
			$this->getUnionSQL()
		);

		// Trim double spaces added by DB::prepare
		return str_replace(
			[ '   ', '  ' ],
			' ',
			implode( ' ', $sql )
		);
	}
}
