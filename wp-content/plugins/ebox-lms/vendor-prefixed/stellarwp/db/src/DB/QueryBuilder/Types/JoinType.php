<?php
/**
 * @license GPL-2.0
 *
 * Modified by ebox on 13-April-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\ebox\StellarWP\DB\QueryBuilder\Types;

/**
 * @since 1.0.0
 */
class JoinType extends Type {
    const INNER = 'INNER';
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';
}
