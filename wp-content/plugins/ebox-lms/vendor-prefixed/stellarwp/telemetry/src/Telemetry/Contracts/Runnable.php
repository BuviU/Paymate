<?php
/**
 * Provides an API for all classes that are runnable.
 *
 * @since 1.0.0
 *
 * @package StellarWP\ebox\StellarWP\Telemetry\Contracts
 *
 * @license GPL-2.0-or-later
 * Modified by ebox on 13-April-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\ebox\StellarWP\Telemetry\Contracts;

/**
 * Provides an API for all classes that are runnable.
 *
 * @since 1.0.0
 *
 * @package StellarWP\ebox\StellarWP\Telemetry\Contracts
 */
interface Runnable {
	/**
	 * Run the intended action.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run();
}
