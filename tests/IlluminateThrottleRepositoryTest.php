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
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Carbon\Carbon;
use Cartalyst\Sentry\Throttling\IlluminateThrottleRepository;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class IlluminateThrottleRepositoryTest extends PHPUnit_Framework_TestCase {

	/**
	 * Close mockery.
	 *
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	public function testGlobalDelayWithIntegerThreshold()
	{
		$this->markTestIncomplete();

		// $throttle = m::mock('Cartalyst\Sentry\Throttling\IlluminateThrottleRepository[createModel]');
		// $throttle->setGlobalInterval(10);
		// $throttle->setGlobalThresholds(5);

		// $throttle->shouldReceive('createModel')->once()->andReturn($model = m::mock('Cartalyst\Sentry\Throttling\EloquentThrottle'));
		// $model->shouldReceive('newQuery')->once()->andReturn($query = m::mock('Illuminate\Database\Eloquent\Builder'));
		// $query->shouldReceive('where')->with('type', 'global')->once()->andReturn($query);

		// $me = $this;
		// $query->shouldReceive('where')->with('created_at', '>', m::on(function($interval) use ($me)
		// {
		// 	$me->assertEquals(time() - 10, $interval->getTimestamp());
		// 	return true;
		// }))->once()->andReturn($query);
		// $query->shouldReceive('get')->once()->andReturn($models = m::mock('Illuminate\Database\Eloquent\Collection'));

		// $models->shouldReceive('count')->once()->andReturn(6);
		// $models->shouldReceive('first')->once()->andReturn($first = m::mock('Cartalyst\Sentry\Throttling\EloquentThrottle'));
		// // $first->shouldReceive('getAttribute')->with('created_at')->andReturn(Carbon::createFromTimestamp(time() - 1));
		// $this->assertEquals(9, $throttle->globalDelay());
	}

}
