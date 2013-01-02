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
use Cartalyst\Sentry\Users\UserNotFoundException;

class SentryTest extends PHPUnit_Framework_TestCase {

	protected $hasher;

	protected $session;

	protected $cookie;

	protected $groupProvider;

	protected $userProvider;

	protected $throttleProvider;

	protected $sentry;

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public function setUp()
	{
		$this->hasher           = m::mock('Cartalyst\Sentry\Hashing\HasherInterface');
		$this->session          = m::mock('Cartalyst\Sentry\Sessions\SessionInterface');
		$this->cookie           = m::mock('Cartalyst\Sentry\Cookies\CookieInterface');
		$this->groupProvider    = m::mock('Cartalyst\Sentry\Groups\ProviderInterface');
		$this->userProvider     = m::mock('Cartalyst\Sentry\Users\ProviderInterface');
		$this->throttleProvider = m::mock('Cartalyst\Sentry\Throttling\ProviderInterface');

		$this->sentry = new Sentry(
			$this->hasher,
			$this->session,
			$this->cookie,
			$this->groupProvider,
			$this->userProvider,
			$this->throttleProvider
		);
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

	/**
	 * @expectedException Cartalyst\Sentry\Users\UserNotActivatedException
	 */
	public function testForceLoggingInUnactivatedUser()
	{
		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('isActivated')->once()->andReturn(false);
		$user->shouldReceive('getUserLogin')->once()->andReturn('foo');

		$this->sentry->forceLogin($user);
	}

	public function testForceLoggingInUser()
	{
		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('isActivated')->once()->andReturn(true);

		$this->session->shouldReceive('getKey')->once()->andReturn('foo');
		$this->session->shouldReceive('put')->with('foo', $user)->once();

		$this->cookie->shouldReceive('getKey')->never();

		$this->sentry->forceLogin($user);
	}

	public function testLoggingInUser()
	{
		$this->sentry = m::mock('Cartalyst\Sentry\Sentry[forceLogin]');
		$this->sentry->__construct(
			$this->hasher,
			$this->session,
			$this->cookie,
			$this->groupProvider,
			$this->userProvider,
			$this->throttleProvider
		);

		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('clearResetPassword')->once();

		$credentials = array(
			'email'    => 'foo@bar.com',
			'password' => 'baz_bat',
		);

		$this->throttleProvider->shouldReceive('isEnabled')->once()->andReturn(false);

		$this->userProvider->shouldReceive('findByCredentials')->with($credentials)->once()->andReturn($user);

		$this->sentry->shouldReceive('forceLogin')->with($user, false)->once();
		$this->sentry->login($credentials);
	}

	public function testLoggingInUserWithThrottling()
	{
		$this->sentry = m::mock('Cartalyst\Sentry\Sentry[forceLogin]');
		$this->sentry->__construct(
			$this->hasher,
			$this->session,
			$this->cookie,
			$this->groupProvider,
			$this->userProvider,
			$this->throttleProvider
		);

		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('getUserId')->once()->andReturn(123);
		$user->shouldReceive('clearResetPassword')->once();

		$credentials = array(
			'email'    => 'foo@bar.com',
			'password' => 'baz_bat',
		);

		$throttle = m::mock('Cartalyst\Sentry\Throttling\ThrottleInterface');
		$throttle->shouldReceive('check')->once();
		$throttle->shouldReceive('clearLoginAttempts')->once();

		$this->throttleProvider->shouldReceive('isEnabled')->once()->andReturn(true);
		$this->throttleProvider->shouldReceive('findByUserId')->with(123)->once()->andReturn($throttle);

		$this->userProvider->shouldReceive('findByCredentials')->with($credentials)->once()->andReturn($user);

		$this->sentry->shouldReceive('forceLogin')->with($user, false)->once();
		$this->sentry->login($credentials);
	}

	/**
	 * @expectedException Cartalyst\Sentry\Users\UserNotFoundException
	 */
	public function testLoggingInUserWhereTheUserDoesNotExist()
	{
		$credentials = array(
			'email'    => 'foo@bar.com',
			'password' => 'baz_bat',
		);

		$this->throttleProvider->shouldReceive('isEnabled')->once()->andReturn(false);

		$this->userProvider->shouldReceive('findByCredentials')->with($credentials)->once()->andThrow(new UserNotFoundException);
		$this->sentry->login($credentials);
	}

	/**
	 * @expectedException Cartalyst\Sentry\Users\UserNotFoundException
	 */
	public function testLoggingInUserWhereTheUserDoesNotExistWithThrottling()
	{
		$credentials = array(
			'email'    => 'foo@bar.com',
			'password' => 'baz_bat',
		);

		$throttle = m::mock('Cartalyst\Sentry\Throttling\ThrottleInterface');
		$throttle->shouldReceive('addLoginAttempt');

		$this->throttleProvider->shouldReceive('isEnabled')->once()->andReturn(true);
		$this->throttleProvider->shouldReceive('findByUserLogin')->once()->with('foo@bar.com')->andReturn($throttle);

		$this->userProvider->shouldReceive('getUserLoginName')->once()->andReturn('email');

		$this->userProvider->shouldReceive('findByCredentials')->with($credentials)->once()->andThrow(new UserNotFoundException);
		$this->sentry->login($credentials);
	}

	public function testLoggingInUserAndRemembering()
	{
		$this->sentry = m::mock('Cartalyst\Sentry\Sentry[login]');

		$credentials = array(
			'email'    => 'foo@bar.com',
			'password' => 'baz_bat',
		);

		$this->sentry->shouldReceive('login')->with($credentials, true)->once();
		$this->sentry->loginAndRemember($credentials);
	}

	public function checkLoggingOut()
	{
		$this->session->shouldReceive('flush')->once();
		$this->cookie->shouldReceive('flush')->once();
		$this->sentry->logout();
		$this->assertNull($this->sentry->getUser());
	}

	public function testCheckingUserWhenUserIsSetAndActivated()
	{
		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('isActivated')->once()->andReturn(true);

		$this->sentry->setUser($user);
		$this->assertTrue($this->sentry->check());
	}

	public function testCheckingUserWhenUserIsSetAndNotActivated()
	{
		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('isActivated')->once()->andReturn(false);

		$this->sentry->setUser($user);
		$this->assertFalse($this->sentry->check());
	}

	public function testCheckingUserChecksSessionFirst()
	{
		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('isActivated')->once()->andReturn(true);

		$this->session->shouldReceive('getKey')->once()->andReturn('foo');
		$this->session->shouldReceive('get')->with('foo')->once()->andReturn($user);

		$this->cookie->shouldReceive('getKey')->never();
		$this->cookie->shouldReceive('get')->never();

		$this->assertTrue($this->sentry->check());
	}

	public function testCheckingUserChecksSessionFirstAndThenCookie()
	{
		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('isActivated')->once()->andReturn(true);

		$this->session->shouldReceive('getKey')->once()->andReturn('foo');
		$this->session->shouldReceive('get')->with('foo')->once()->andReturn(null);

		$this->cookie->shouldReceive('getKey')->once()->andReturn('foo');
		$this->cookie->shouldReceive('get')->with('foo')->once()->andReturn($user);

		$this->assertTrue($this->sentry->check());
	}

	public function testCheckingUserWhenNothingIsFound()
	{
		$this->session->shouldReceive('getKey')->once()->andReturn('foo');
		$this->session->shouldReceive('get')->with('foo')->once()->andReturn(null);

		$this->cookie->shouldReceive('getKey')->once()->andReturn('foo');
		$this->cookie->shouldReceive('get')->with('foo')->once()->andReturn(null);

		$this->assertFalse($this->sentry->check());
	}

	public function testRegisteringUser()
	{
		$credentials = array(
			'email'    => 'foo@bar.com',
			'password' => 'sdf_sdf',
		);

		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('getActivationCode')->never();
		$user->shouldReceive('attemptActivation')->never();
		$user->shouldReceive('isActivated')->once()->andReturn(false);

		$this->userProvider->shouldReceive('register')->with($credentials)->once()->andReturn($user);

		$this->assertEquals($user, $registeredUser = $this->sentry->register($credentials));
		$this->assertFalse($registeredUser->isActivated());
	}

	public function testRegisteringUserWithActivationDone()
	{
		$credentials = array(
			'email'    => 'foo@bar.com',
			'password' => 'sdf_sdf',
		);

		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('getActivationCode')->once()->andReturn('activation_code_here');
		$user->shouldReceive('attemptActivation')->with('activation_code_here')->once();
		$user->shouldReceive('isActivated')->once()->andReturn(true);

		$this->userProvider->shouldReceive('register')->with($credentials)->once()->andReturn($user);

		$this->assertEquals($user, $registeredUser = $this->sentry->register($credentials, true));
		$this->assertTrue($registeredUser->isActivated());
	}

}