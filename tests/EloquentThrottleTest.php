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

class EloquentThrottleText extends PHPUnit_Framework_TestCase {

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

		$throttle = new Throttle;
		$throttle->last_attempt_at = $dateTime;

		$this->assertEquals($dateTime, $throttle->last_attempt_at);

		$expected = array(
			'last_attempt_at' => $actualDate,
		);
		$this->assertEquals($expected, $throttle->toArray());

		$expected = "{\"last_attempt_at\":\"$actualDate\"}";
		$this->assertEquals($expected, (string) $throttle);
	}

}