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

use Cartalyst\Sentry\Sessions\NativeSession;
use Mockery as m;
use PHPUnit_Framework_TestCase;
use stdClass;

class NativeSessionTest extends PHPUnit_Framework_TestCase {

	/**
	 * Close mockery.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	public function testPut()
	{
		$session = new NativeSession('__sentry');

		$class = new stdClass;
		$class->foo = 'bar';

		$session->put($class);
		$this->assertEquals(serialize($class), $_SESSION['__sentry']);
		unset($_SESSION['__sentry']);
	}

	public function testGet()
	{
		$session = new NativeSession('__sentry');
		$this->assertNull($session->get());

		$class = new stdClass;
		$class->foo = 'bar';
		$_SESSION['__sentry'] = serialize($class);

		$this->assertEquals($class, $session->get());
		unset($_SESSION['__sentry']);
	}

	public function testForget()
	{
		$_SESSION['__sentry'] = 'bar';

		$session = new NativeSession('__sentry');

		$this->assertEquals('bar', $_SESSION['__sentry']);
		$session->forget();
		$this->assertFalse(isset($_SESSION['__sentry']));
	}

}
