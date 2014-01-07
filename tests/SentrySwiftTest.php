<?php namespace Cartalyst\Sentry\Tests;
/**
 * Part of the Sentry package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Sentry
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Swift\SentrySwift;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class SentrySwiftTest extends PHPUnit_Framework_TestCase {

	/**
	 * Close mockery.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	public function testResponse()
	{
		$swift = new SentrySwift('email@example.com', 'password', 'key', 'code', '127.0.0.1');

		$this->markTestIncomplete();
	}

	public function testSaveNumber()
	{
		$swift = new SentrySwift('email@example.com', 'password', 'key', 'code', '127.0.0.1');

		$this->markTestIncomplete();
	}

	public function testCheckAnswer()
	{
		$swift = new SentrySwift('email@example.com', 'password', 'key', 'code', '127.0.0.1');

		$this->markTestIncomplete();
	}

}
