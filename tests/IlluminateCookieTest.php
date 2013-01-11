<?php
/**
 * Part of the Sentry Package.
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
 * @version    2.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Mockery as m;
use Cartalyst\Sentry\Cookies\IlluminateCookie;

class IlluminateCookieTest extends PHPUnit_Framework_TestCase {

	protected $jar;

	protected $cookie;

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->jar    = m::mock('Illuminate\Cookie\CookieJar');
		$this->cookie = new IlluminateCookie($this->jar);
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

	public function testOverridingKey()
	{
		$this->jar->shouldReceive('make')->with('foo', 'bar', 123)->once();
		$this->cookie->put('foo', 'bar', 123);
		$this->assertEquals(1, count($this->cookie->getQueuedCookies()));
	}

	public function testPut()
	{
		$this->jar->shouldReceive('make')->with('foo', 'bar', 123)->once();
		$this->cookie->put('foo', 'bar', 123);
		$this->assertEquals(1, count($this->cookie->getQueuedCookies()));
	}

	public function testForever()
	{
		$this->jar->shouldReceive('forever')->with('foo', 'bar')->once();
		$this->cookie->forever('foo', 'bar');
		$this->assertEquals(1, count($this->cookie->getQueuedCookies()));
	}

	public function testGet()
	{
		$this->jar->shouldReceive('get')->with('foo', null)->twice()->andReturn('bar');

		// Ensure default param is "null"
		$this->assertEquals('bar', $this->cookie->get('foo'));
		$this->assertEquals('bar', $this->cookie->get('foo', null));
		$this->assertEquals(0, count($this->cookie->getQueuedCookies()));
	}

	public function testForget()
	{
		$this->jar->shouldReceive('forget')->with('foo')->once();
		$this->cookie->forget('foo');
		$this->assertEquals(1, count($this->cookie->getQueuedCookies()));
	}

	public function testFlush()
	{
		$this->jar->shouldReceive('forget')->with($this->cookie->getKey())->once();
		$this->cookie->flush();
	}

}