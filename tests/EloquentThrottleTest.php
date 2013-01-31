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
use Cartalyst\Sentry\Throttling\Eloquent\Throttle;

class EloquentThrottleTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup resources and dependencies.
	 *
	 * @return void
	 */
	public function setUp()
	{

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

	public function testGettingUserReturnsUserObject()
	{
		$user = m::mock('StdClass');
		$user->shouldReceive('getResults')->once()->andReturn('foo');

		$throttle = m::mock('Cartalyst\Sentry\Throttling\Eloquent\Throttle[user]');
		$throttle->shouldReceive('user')->once()->andReturn($user);

		$this->assertEquals('foo', $throttle->getUser());
	}

	public function testAttemptLimits()
	{
		$throttle = new Throttle;
		$throttle->setAttemptLimit(15);
		$this->assertEquals(15, $throttle->getAttemptLimit());
	}

	public function testDateTimeObjectIsUsedForLastAttemptAt()
	{
		$actualDate = '2013-01-01 00:00:00';
		$dateTime = new DateTime($actualDate);

		$connection = m::mock('StdClass');
		$connection->shouldReceive('getQueryGrammar')->atLeast(1)->andReturn($connection);
		$connection->shouldReceive('getDateFormat')->atLeast(1)->andReturn('Y-m-d H:i:s');

		$throttle = m::mock('Cartalyst\Sentry\Throttling\Eloquent\Throttle[getConnection]');
		$throttle->shouldReceive('getConnection')->atLeast(1)->andReturn($connection);
		$throttle->last_attempt_at = $dateTime;

		$this->assertEquals($dateTime, $throttle->last_attempt_at);

		$expected = array(
			'last_attempt_at' => $actualDate,
		);
		$this->assertEquals($expected, $throttle->toArray());

		$expected = "{\"last_attempt_at\":\"$actualDate\"}";
		$this->assertEquals($expected, (string) $throttle);
	}

	public function testGettingLoginAttemptsWhenNoAttemptHasBeenMadeBefore()
	{
		$throttle = m::mock('Cartalyst\Sentry\Throttling\Eloquent\Throttle[clearLoginAttemptsIfAllowed]');
		$throttle->shouldReceive('clearLoginAttemptsIfAllowed')->never();

		$this->assertEquals(0, $throttle->getLoginAttempts());
		$throttle->attempts = 1;
		$this->assertEquals(1, $throttle->getLoginAttempts());
	}

	public function testGettingLoginAttemptsResetsIfSuspensionTimeHasPassedSinceLastAttempt()
	{
		$connection = m::mock('StdClass');
		$connection->shouldReceive('getQueryGrammar')->atLeast(1)->andReturn($connection);
		$connection->shouldReceive('getDateFormat')->atLeast(1)->andReturn('Y-m-d H:i:s');

		$throttle = m::mock('Cartalyst\Sentry\Throttling\Eloquent\Throttle[save,getConnection]');;
		$throttle->shouldReceive('getConnection')->atLeast(1)->andReturn($connection);

		// Let's simulate that the suspension time
		// is 11 minutes however the last attempt was
		// 10 minutes ago, we'll not reset the attempts
		$throttle->setSuspensionTime(11);
		$lastAttemptAt = new DateTime;
		$lastAttemptAt->modify('-10 minutes');

		$throttle->last_attempt_at = $lastAttemptAt;
		$throttle->attempts = 3;
		$this->assertEquals(3, $throttle->getLoginAttempts());

		// Suspension time is 9 minutes now,
		// our attempts shall be reset
		$throttle->shouldReceive('save')->once();
		$throttle->setSuspensionTime(9);
		$this->assertEquals(0, $throttle->getLoginAttempts());
	}

	public function testSuspend()
	{
		$connection = m::mock('StdClass');
		$connection->shouldReceive('getQueryGrammar')->atLeast(1)->andReturn($connection);
		$connection->shouldReceive('getDateFormat')->atLeast(1)->andReturn('Y-m-d H:i:s');

		$throttle = m::mock('Cartalyst\Sentry\Throttling\Eloquent\Throttle[save,getConnection]');;
		$throttle->shouldReceive('getConnection')->atLeast(1)->andReturn($connection);
		$throttle->shouldReceive('save')->once();

		$this->assertNull($throttle->suspended_at);
		$throttle->suspend();

		$this->assertNotNull($throttle->suspended_at);
		$this->assertTrue($throttle->suspended);
	}

	public function testUnsuspend()
	{
		$connection = m::mock('StdClass');
		$connection->shouldReceive('getQueryGrammar')->atLeast(1)->andReturn($connection);
		$connection->shouldReceive('getDateFormat')->atLeast(1)->andReturn('Y-m-d H:i:s');

		$throttle = m::mock('Cartalyst\Sentry\Throttling\Eloquent\Throttle[save,getConnection]');;
		$throttle->shouldReceive('getConnection')->atLeast(1)->andReturn($connection);

		$throttle->shouldReceive('save')->once();

		$lastAttemptAt = new DateTime;
		$suspendedAt   = new DateTime;

		$throttle->attempts        = 3;
		$throttle->last_attempt_at = $lastAttemptAt;
		$throttle->suspended       = true;
		$throttle->suspended_at    = $suspendedAt;

		$throttle->unsuspend();

		$this->assertEquals(0, $throttle->attempts);
		$this->assertNull($throttle->last_attempt_at);
		$this->assertFalse($throttle->suspended);
		$this->assertNull($throttle->suspended_at);
	}

	// public function testIsSuspended()
	// {
	// 	$throttle = new Throttle;
	// 	$this->assertFalse($throttle->isSuspended());
	// }

	// public function testIsSuspendedRemovesSuspensionIfEnoughTimeHasPassed()
	// {
	// 	$throttle = m::mock('Cartalyst\Sentry\Throttling\Eloquent\Throttle[save]');
	// 	$throttle->shouldReceive('save')->once();
	// 	$throttle->suspended = true;


	// 	// Still suspended
	// 	$throttle->setSuspensionTime(11);
	// 	$suspendedAt = new DateTime;
	// 	$suspendedAt->modify('-10 minutes');
	// 	$throttle->suspended_at = $suspendedAt;

	// 	$this->assertTrue($throttle->isSuspended());

	// 	// Unsuspend time, because suspension time is 9
	// 	// minutes however we were suspended at 10 minutes
	// 	// ago
	// 	$throttle->setSuspensionTime(9);
	// 	$this->assertFalse($throttle->isSuspended());
	// }

	// public function testAddLoginAttempt()
	// {
	// 	$throttle = m::mock('Cartalyst\Sentry\Throttling\Eloquent\Throttle[suspend,save]');
	// 	$throttle->shouldReceive('save')->once();
	// 	$throttle->shouldReceive('suspend')->once();

	// 	$throttle->setAttemptLimit(5);
	// 	$throttle->attempts = 3;

	// 	$throttle->addLoginAttempt();
	// 	$this->assertEquals(4, $throttle->getLoginAttempts());

	// 	$throttle->addLoginAttempt();
	// 	$this->assertEquals(5, $throttle->getLoginAttempts());
	// }

	// public function testBanning()
	// {
	// 	$throttle = m::mock('Cartalyst\Sentry\Throttling\Eloquent\Throttle[save]');
	// 	$throttle->shouldReceive('save')->twice();

	// 	$throttle->ban();
	// 	$this->assertTrue($throttle->isBanned());
	// 	$throttle->unban();
	// 	$this->assertFalse($throttle->isBanned());
	// }

	// /**
	//  * @expectedException Cartalyst\Sentry\Throttling\UserBannedException
	//  */
	// public function testCheckingThrowsProperExceptionWhenUserIsBanned()
	// {
	// 	$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
	// 	$user->shouldReceive('getLogin')->once()->andReturn('foo');

	// 	$throttle = m::mock('Cartalyst\Sentry\Throttling\Eloquent\Throttle[isBanned,isSuspended,getUser]');
	// 	$throttle->shouldReceive('isBanned')->once()->andReturn(true);
	// 	$throttle->shouldReceive('isSuspended')->never();
	// 	$throttle->shouldReceive('getUser')->once()->andReturn($user);

	// 	$throttle->check();
	// }

	// /**
	//  * @expectedException Cartalyst\Sentry\Throttling\UserSuspendedException
	//  */
	// public function testCheckingThrowsProperExceptionWhenUserIsSuspended()
	// {
	// 	$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
	// 	$user->shouldReceive('getLogin')->once()->andReturn('foo');

	// 	$throttle = m::mock('Cartalyst\Sentry\Throttling\Eloquent\Throttle[isBanned,isSuspended,getUser]');
	// 	$throttle->shouldReceive('isBanned')->once()->andReturn(false);
	// 	$throttle->shouldReceive('isSuspended')->once()->andReturn(true);
	// 	$throttle->shouldReceive('getUser')->once()->andReturn($user);

	// 	$throttle->check();
	// }

	// public function testCheckingWhenUserIsOkay()
	// {
	// 	$throttle = m::mock('Cartalyst\Sentry\Throttling\Eloquent\Throttle[isBanned,isSuspended]');
	// 	$throttle->shouldReceive('isBanned')->once()->andReturn(false);
	// 	$throttle->shouldReceive('isSuspended')->once()->andReturn(false);

	// 	$this->assertTrue($throttle->check());
	// }

	// public function testEnabling()
	// {
	// 	$throttle = new Throttle;
	// 	$throttle->enable();
	// 	$this->assertTrue($throttle->isEnabled());
	// 	$throttle->disable();
	// 	$this->assertFalse($throttle->isEnabled());
	// }

}
