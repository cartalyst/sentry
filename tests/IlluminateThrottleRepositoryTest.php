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
use Cartalyst\Sentry\Throttling\EloquentThrottle;
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
		$throttle = m::mock('Cartalyst\Sentry\Throttling\IlluminateThrottleRepository[createModel]');
		$throttle->setGlobalInterval(10);
		$throttle->setGlobalThresholds(5);

		$throttle->shouldReceive('createModel')->andReturn($model = m::mock('Cartalyst\Sentry\Throttling\EloquentThrottle[newQuery]'));
		$model->shouldReceive('newQuery')->andReturn($query = m::mock('Illuminate\Database\Eloquent\Builder'));
		$query->shouldReceive('where')->with('type', 'global')->andReturn($query);

		$me = $this;
		$query->shouldReceive('where')->with('created_at', '>', m::on(function($interval) use ($me)
		{
			$me->assertEquals(time() - 10, $interval->getTimestamp());
			return true;
		}))->andReturn($query);
		$query->shouldReceive('get')->andReturn($models = m::mock('Illuminate\Database\Eloquent\Collection'));

		$models->shouldReceive('count')->andReturn(6);
		$models->shouldReceive('first')->andReturn($first = new EloquentThrottle);
		$this->addMockConnection($first);
		$first->getConnection()->getQueryGrammar()->shouldReceive('getDateFormat')->andReturn('Y-m-d H:i:s');
		$first->created_at = Carbon::createFromTimestamp(time() - 1);

		$this->assertEquals(9, $throttle->globalDelay());
	}

	public function testGlobalDelayWithArrayThresholds1()
	{
		$throttle = m::mock('Cartalyst\Sentry\Throttling\IlluminateThrottleRepository[createModel]');
		$throttle->setGlobalInterval(10);
		$throttle->setGlobalThresholds(array(5 => 3, 10 => 10));

		$throttle->shouldReceive('createModel')->andReturn($model = m::mock('Cartalyst\Sentry\Throttling\EloquentThrottle[newQuery]'));
		$model->shouldReceive('newQuery')->andReturn($query = m::mock('Illuminate\Database\Eloquent\Builder'));
		$query->shouldReceive('where')->with('type', 'global')->andReturn($query);

		$me = $this;
		$query->shouldReceive('where')->with('created_at', '>', m::on(function($interval) use ($me)
		{
			$me->assertEquals(time() - 10, $interval->getTimestamp());
			return true;
		}))->andReturn($query);
		$query->shouldReceive('get')->andReturn($models = m::mock('Illuminate\Database\Eloquent\Collection'));

		$models->shouldReceive('count')->andReturn(6);
		$models->shouldReceive('last')->andReturn($last = new EloquentThrottle);
		$this->addMockConnection($last);
		$last->getConnection()->getQueryGrammar()->shouldReceive('getDateFormat')->andReturn('Y-m-d H:i:s');
		$last->created_at = Carbon::createFromTimestamp(time() - 1);

		$this->assertEquals(2, $throttle->globalDelay());
	}

	public function testGlobalDelayWithArrayThresholds2()
	{
		$throttle = m::mock('Cartalyst\Sentry\Throttling\IlluminateThrottleRepository[createModel]');
		$throttle->setGlobalInterval(10);
		$throttle->setGlobalThresholds(array(5 => 3, 10 => 10));

		$throttle->shouldReceive('createModel')->andReturn($model = m::mock('Cartalyst\Sentry\Throttling\EloquentThrottle[newQuery]'));
		$model->shouldReceive('newQuery')->andReturn($query = m::mock('Illuminate\Database\Eloquent\Builder'));
		$query->shouldReceive('where')->with('type', 'global')->andReturn($query);

		$me = $this;
		$query->shouldReceive('where')->with('created_at', '>', m::on(function($interval) use ($me)
		{
			$me->assertEquals(time() - 10, $interval->getTimestamp());
			return true;
		}))->andReturn($query);
		$query->shouldReceive('get')->andReturn($models = m::mock('Illuminate\Database\Eloquent\Collection'));

		$models->shouldReceive('count')->andReturn(11);
		$models->shouldReceive('last')->andReturn($last = new EloquentThrottle);
		$this->addMockConnection($last);
		$last->getConnection()->getQueryGrammar()->shouldReceive('getDateFormat')->andReturn('Y-m-d H:i:s');
		$last->created_at = Carbon::createFromTimestamp(time() - 1);

		$this->assertEquals(9, $throttle->globalDelay());
	}

	public function testIpDelayWithIntegerThreshold()
	{
		$throttle = m::mock('Cartalyst\Sentry\Throttling\IlluminateThrottleRepository[createModel]');
		$throttle->setIpInterval(10);
		$throttle->setIpThresholds(5);

		$throttle->shouldReceive('createModel')->andReturn($model = m::mock('Cartalyst\Sentry\Throttling\EloquentThrottle[newQuery]'));
		$model->shouldReceive('newQuery')->andReturn($query = m::mock('Illuminate\Database\Eloquent\Builder'));
		$query->shouldReceive('where')->with('type', 'ip')->andReturn($query);
		$query->shouldReceive('where')->with('ip', '127.0.0.1')->andReturn($query);

		$me = $this;
		$query->shouldReceive('where')->with('created_at', '>', m::on(function($interval) use ($me)
		{
			$me->assertEquals(time() - 10, $interval->getTimestamp());
			return true;
		}))->andReturn($query);
		$query->shouldReceive('get')->andReturn($models = m::mock('Illuminate\Database\Eloquent\Collection'));

		$models->shouldReceive('count')->andReturn(6);
		$models->shouldReceive('first')->andReturn($first = new EloquentThrottle);
		$this->addMockConnection($first);
		$first->getConnection()->getQueryGrammar()->shouldReceive('getDateFormat')->andReturn('Y-m-d H:i:s');
		$first->created_at = Carbon::createFromTimestamp(time() - 1);

		$this->assertEquals(9, $throttle->ipDelay('127.0.0.1'));
	}

	public function testIpDelayWithArrayThresholds1()
	{
		$throttle = m::mock('Cartalyst\Sentry\Throttling\IlluminateThrottleRepository[createModel]');
		$throttle->setIpInterval(10);
		$throttle->setIpThresholds(array(5 => 3, 10 => 10));

		$throttle->shouldReceive('createModel')->andReturn($model = m::mock('Cartalyst\Sentry\Throttling\EloquentThrottle[newQuery]'));
		$model->shouldReceive('newQuery')->andReturn($query = m::mock('Illuminate\Database\Eloquent\Builder'));
		$query->shouldReceive('where')->with('type', 'ip')->andReturn($query);
		$query->shouldReceive('where')->with('ip', '127.0.0.1')->andReturn($query);

		$me = $this;
		$query->shouldReceive('where')->with('created_at', '>', m::on(function($interval) use ($me)
		{
			$me->assertEquals(time() - 10, $interval->getTimestamp());
			return true;
		}))->andReturn($query);
		$query->shouldReceive('get')->andReturn($models = m::mock('Illuminate\Database\Eloquent\Collection'));

		$models->shouldReceive('count')->andReturn(6);
		$models->shouldReceive('last')->andReturn($last = new EloquentThrottle);
		$this->addMockConnection($last);
		$last->getConnection()->getQueryGrammar()->shouldReceive('getDateFormat')->andReturn('Y-m-d H:i:s');
		$last->created_at = Carbon::createFromTimestamp(time() - 1);

		$this->assertEquals(2, $throttle->ipDelay('127.0.0.1'));
	}

	public function testIpDelayWithArrayThresholds2()
	{
		$throttle = m::mock('Cartalyst\Sentry\Throttling\IlluminateThrottleRepository[createModel]');
		$throttle->setIpInterval(10);
		$throttle->setIpThresholds(array(5 => 3, 10 => 10));

		$throttle->shouldReceive('createModel')->andReturn($model = m::mock('Cartalyst\Sentry\Throttling\EloquentThrottle[newQuery]'));
		$model->shouldReceive('newQuery')->andReturn($query = m::mock('Illuminate\Database\Eloquent\Builder'));
		$query->shouldReceive('where')->with('type', 'ip')->andReturn($query);
		$query->shouldReceive('where')->with('ip', '127.0.0.1')->andReturn($query);

		$me = $this;
		$query->shouldReceive('where')->with('created_at', '>', m::on(function($interval) use ($me)
		{
			$me->assertEquals(time() - 10, $interval->getTimestamp());
			return true;
		}))->andReturn($query);
		$query->shouldReceive('get')->andReturn($models = m::mock('Illuminate\Database\Eloquent\Collection'));

		$models->shouldReceive('count')->andReturn(11);
		$models->shouldReceive('last')->andReturn($last = new EloquentThrottle);
		$this->addMockConnection($last);
		$last->getConnection()->getQueryGrammar()->shouldReceive('getDateFormat')->andReturn('Y-m-d H:i:s');
		$last->created_at = Carbon::createFromTimestamp(time() - 1);

		$this->assertEquals(9, $throttle->ipDelay('127.0.0.1'));
	}

	public function testUserDelayWithIntegerThreshold()
	{
		$throttle = m::mock('Cartalyst\Sentry\Throttling\IlluminateThrottleRepository[createModel]');
		$throttle->setUserInterval(10);
		$throttle->setUserThresholds(5);

		$throttle->shouldReceive('createModel')->andReturn($model = m::mock('Cartalyst\Sentry\Throttling\EloquentThrottle[newQuery]'));
		$model->shouldReceive('newQuery')->andReturn($query = m::mock('Illuminate\Database\Eloquent\Builder'));
		$query->shouldReceive('where')->with('type', 'user')->andReturn($query);
		$query->shouldReceive('where')->with('user_id', 1)->andReturn($query);

		$me = $this;
		$query->shouldReceive('where')->with('created_at', '>', m::on(function($interval) use ($me)
		{
			$me->assertEquals(time() - 10, $interval->getTimestamp());
			return true;
		}))->andReturn($query);
		$query->shouldReceive('get')->andReturn($models = m::mock('Illuminate\Database\Eloquent\Collection'));

		$models->shouldReceive('count')->andReturn(6);
		$models->shouldReceive('first')->andReturn($first = new EloquentThrottle);
		$this->addMockConnection($first);
		$first->getConnection()->getQueryGrammar()->shouldReceive('getDateFormat')->andReturn('Y-m-d H:i:s');
		$first->created_at = Carbon::createFromTimestamp(time() - 1);

		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('getUserId')->andReturn(1);
		$this->assertEquals(9, $throttle->userDelay($user));
	}

	public function testUserDelayWithArrayThresholds1()
	{
		$throttle = m::mock('Cartalyst\Sentry\Throttling\IlluminateThrottleRepository[createModel]');
		$throttle->setUserInterval(10);
		$throttle->setUserThresholds(array(5 => 3, 10 => 10));

		$throttle->shouldReceive('createModel')->andReturn($model = m::mock('Cartalyst\Sentry\Throttling\EloquentThrottle[newQuery]'));
		$model->shouldReceive('newQuery')->andReturn($query = m::mock('Illuminate\Database\Eloquent\Builder'));
		$query->shouldReceive('where')->with('type', 'user')->andReturn($query);
		$query->shouldReceive('where')->with('user_id', 1)->andReturn($query);

		$me = $this;
		$query->shouldReceive('where')->with('created_at', '>', m::on(function($interval) use ($me)
		{
			$me->assertEquals(time() - 10, $interval->getTimestamp());
			return true;
		}))->andReturn($query);
		$query->shouldReceive('get')->andReturn($models = m::mock('Illuminate\Database\Eloquent\Collection'));

		$models->shouldReceive('count')->andReturn(6);
		$models->shouldReceive('last')->andReturn($last = new EloquentThrottle);
		$this->addMockConnection($last);
		$last->getConnection()->getQueryGrammar()->shouldReceive('getDateFormat')->andReturn('Y-m-d H:i:s');
		$last->created_at = Carbon::createFromTimestamp(time() - 1);

		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('getUserId')->andReturn(1);
		$this->assertEquals(2, $throttle->userDelay($user));
	}

	public function testUserDelayWithArrayThresholds2()
	{
		$throttle = m::mock('Cartalyst\Sentry\Throttling\IlluminateThrottleRepository[createModel]');
		$throttle->setUserInterval(10);
		$throttle->setUserThresholds(array(5 => 3, 10 => 10));

		$throttle->shouldReceive('createModel')->andReturn($model = m::mock('Cartalyst\Sentry\Throttling\EloquentThrottle[newQuery]'));
		$model->shouldReceive('newQuery')->andReturn($query = m::mock('Illuminate\Database\Eloquent\Builder'));
		$query->shouldReceive('where')->with('type', 'user')->andReturn($query);
		$query->shouldReceive('where')->with('user_id', 1)->andReturn($query);

		$me = $this;
		$query->shouldReceive('where')->with('created_at', '>', m::on(function($interval) use ($me)
		{
			$me->assertEquals(time() - 10, $interval->getTimestamp());
			return true;
		}))->andReturn($query);
		$query->shouldReceive('get')->andReturn($models = m::mock('Illuminate\Database\Eloquent\Collection'));

		$models->shouldReceive('count')->andReturn(11);
		$models->shouldReceive('last')->andReturn($last = new EloquentThrottle);
		$this->addMockConnection($last);
		$last->getConnection()->getQueryGrammar()->shouldReceive('getDateFormat')->andReturn('Y-m-d H:i:s');
		$last->created_at = Carbon::createFromTimestamp(time() - 1);

		$user = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$user->shouldReceive('getUserId')->andReturn(1);
		$this->assertEquals(9, $throttle->userDelay($user));
	}

	protected function addMockConnection($model)
	{
		$model->setConnectionResolver($resolver = m::mock('Illuminate\Database\ConnectionResolverInterface'));
		$resolver->shouldReceive('connection')->andReturn(m::mock('Illuminate\Database\Connection'));
		$model->getConnection()->shouldReceive('getQueryGrammar')->andReturn(m::mock('Illuminate\Database\Query\Grammars\Grammar'));
		$model->getConnection()->shouldReceive('getPostProcessor')->andReturn(m::mock('Illuminate\Database\Query\Processors\Processor'));
	}

}
