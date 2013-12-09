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

use Cartalyst\Sentry\Cookies\IlluminateCookie;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class IlluminateCookieTest extends PHPUnit_Framework_TestCase {

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
		$cookie = new IlluminateCookie($request = m::mock('Illuminate\Http\Request'), $jar = m::mock('Illuminate\Cookie\CookieJar'), 'foo');
		$jar->shouldReceive('forever')->with('foo', 'bar')->once()->andReturn('cookie');
		$jar->shouldReceive('queue')->with('cookie')->once();
		$cookie->put('bar');
	}

	public function testGetWithQueuedCookie()
	{
		$cookie = new IlluminateCookie($request = m::mock('Illuminate\Http\Request'), $jar = m::mock('Illuminate\Cookie\CookieJar'), 'foo');
		$jar->shouldReceive('getQueuedCookies')->once()->andReturn(array('foo' => 'bar'));
		$this->assertEquals('bar', $cookie->get());
	}

	public function testGetWithPreviousCookies()
	{
		$cookie = new IlluminateCookie($request = m::mock('Illuminate\Http\Request'), $jar = m::mock('Illuminate\Cookie\CookieJar'), 'foo');
		$jar->shouldReceive('getQueuedCookies')->once()->andReturn(array());
		$request->shouldReceive('cookie')->with('foo')->once()->andReturn('bar');
		$this->assertEquals('bar', $cookie->get());
	}

	public function testForget()
	{
		$cookie = new IlluminateCookie($request = m::mock('Illuminate\Http\Request'), $jar = m::mock('Illuminate\Cookie\CookieJar'), 'foo');
		$jar->shouldReceive('forget')->with('foo')->once()->andReturn('cookie');
		$jar->shouldReceive('queue')->with('cookie')->once();
		$cookie->forget();
	}

}
