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
use Cartalyst\Sentry\Sentry;

class SentryTest extends PHPUnit_Framework_TestCase {

	protected $provider;

	protected $session;

	protected $cookie;

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->provider = m::mock('Cartalyst\Sentry\ProviderInterface');
		$this->session  = m::mock('Cartalyst\Sentry\SessionInterface');
		$this->cookie   = m::mock('Cartalyst\Sentry\CookieInterface');

		$this->sentry = new Sentry($this->provider, $this->session, $this->cookie);
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

	public function testCallingUserCallsProvider()
	{
		$this->provider->shouldReceive('userInterface')->once();
		$this->sentry->user();
	}

	public function testCallingGroupCallsProvider()
	{
		$this->provider->shouldReceive('groupInterface')->once();
		$this->sentry->group();
	}

	public function testCallingThrottleCallsProvider()
	{
		$this->provider->shouldReceive('throttleInterface')->once();
		$this->sentry->throttle();
	}

	public function testLoggingInUser()
	{
		$user = m::mock('Cartalyst\Sentry\UserInterface');
		$user->exists = true;
		$user->shouldReceive('isActivated')->once()->andReturn(true);

		$this->session->shouldReceive('getKey')->once()->andReturn('foo');
		$this->session->shouldReceive('put')->with('foo', $user)->once();

		$this->cookie->shouldReceive('key')->never();
		$this->cookie->shouldReceive('forever')->never();

		$this->sentry->login($user);
	}

	public function testLoggingInUserAndRememberingLogin()
	{
		$user = m::mock('Cartalyst\Sentry\UserInterface');
		$user->exists = true;
		$user->shouldReceive('isActivated')->once()->andReturn(true);

		$this->session->shouldReceive('getKey')->once()->andReturn('foo');
		$this->session->shouldReceive('put')->with('foo', $user)->once();

		$this->cookie->shouldReceive('getKey')->once()->andReturn('foo');
		$this->cookie->shouldReceive('forever')->with('foo', $user)->once();

		$this->sentry->login($user, true);
	}

	public function testLoginAndRememberCallsCorrectMethod()
	{
		$user = m::mock('Cartalyst\Sentry\UserInterface');

		$this->sentry = m::mock('Cartalyst\Sentry\Sentry[login]');
		$this->sentry->shouldReceive('login')->with($user, true)->once();

		$this->sentry->loginAndRemember($user);
	}

	public function testCheckingLoginOnlyTestsSessionIfSuccessful()
	{
		$user = m::mock('Cartalyst\Sentry\UserInterface');
		$this->session->shouldReceive('getKey')->once()->andReturn('foo');
		$this->session->shouldReceive('get')->with('foo')->once()->andReturn('user');

		$this->cookie->shouldReceive('getKey')->never();
		$this->cookie->shouldReceive('get')->never();

		$this->assertTrue($this->sentry->check());
	}

	public function testCheckingLoginWillTryCookieIfSessionIsUnsuccessful()
	{
		$user = m::mock('Cartalyst\Sentry\UserInterface');
		$this->session->shouldReceive('getKey')->once()->andReturn('foo');
		$this->session->shouldReceive('get')->with('foo')->once()->andReturn(null);

		$this->cookie->shouldReceive('getKey')->once()->andReturn('foo');
		$this->cookie->shouldReceive('get')->with('foo')->once()->andReturn($user);

		$this->assertTrue($this->sentry->check());
	}

	public function testCheckingLoginFailsIfNoUserIsPresentInTheSessionOrCookie()
	{
		$this->session->shouldReceive('getKey')->once()->andReturn('foo');
		$this->session->shouldReceive('get')->with('foo')->once()->andReturn(null);

		$this->cookie->shouldReceive('getKey')->once()->andReturn('foo');
		$this->cookie->shouldReceive('get')->with('foo')->once()->andReturn(null);

		$this->assertFalse($this->sentry->check());
	}

	public function testLoggingOutFlushesCookiesAndSession()
	{
		$this->session->shouldReceive('flush')->once();
		$this->cookie->shouldReceive('flush')->once();

		$this->sentry->logout();
	}

}