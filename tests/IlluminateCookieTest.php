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

	protected $cookie;

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public function setUp()
	{


		$this->cookie = m::mock('Cartalyst\Sentry\Cookies\IlluminateCookie[setCookie]');
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
		$this->cookie->shouldReceive('setCookie')->with('foo', 'bar')->once();

		$this->session->put('foo', 'bar');
	}

	public function testGet()
	{
		$this->store->shouldReceive('get')->with('foo', null)->twice()->andReturn('bar');

		// Test with default "null" param as well
		$this->assertEquals('bar', $this->session->get('foo'));
		$this->assertEquals('bar', $this->session->get('foo', null));
	}

	public function testForget()
	{
		$this->store->shouldReceive('forget')->with('foo')->once();

		$this->session->forget('foo');
	}

	public function testFlush()
	{
		$this->store->shouldReceive('forget')->with($this->session->getKey())->once();

		$this->session->flush();
	}

}