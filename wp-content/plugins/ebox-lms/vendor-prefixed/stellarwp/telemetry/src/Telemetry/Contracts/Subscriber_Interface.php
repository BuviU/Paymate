<?php
/**
 * The API implemented by all subscribers.
 *
 * @package StellarWP\ebox\StellarWP\Telemetry\Contracts
 *
 * @license GPL-2.0-or-later
 * Modified by ebox on 13-April-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace StellarWP\ebox\StellarWP\Telemetry\Contracts;

/**
 * Interface Subscriber_Interface
 *
 * @package StellarWP\ebox\StellarWP\Telemetry\Contracts
 */
interface Subscriber_Interface {

	/**
	 * Register action/filter listeners to hook into WordPress
	 *
	 * @return void
	 */
	public function register();

}
