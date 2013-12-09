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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Sessions\KohanaSession;
use Mockery as m;
use PHPUnit_Framework_TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class KohanaSessionTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public static function setUpBeforeClass()
	{
		require_once __DIR__.'/stubs/kohana/Kohana/Session.php';
	}

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
		$session = new KohanaSession($store = m::mock('Kohana_Session'), 'foo');
		$store->shouldReceive('set')->with('foo', serialize('bar'))->once();
		$session->put('bar');
	}

	public function testGet()
	{
		$session = new KohanaSession($store = m::mock('Kohana_Session'), 'foo');
		$store->shouldReceive('get')->with('foo')->once()->andReturn(serialize('bar'));
		$this->assertEquals('bar', $session->get());
	}

	public function testForget()
	{
		$session = new KohanaSession($store = m::mock('Kohana_Session'), 'foo');
		$store->shouldReceive('delete')->with('foo')->once();
		$session->forget();
	}

}
