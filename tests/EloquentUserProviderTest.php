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
use Cartalyst\Sentry\Users\Eloquent\Provider;

class EloquentUserProviderTest extends PHPUnit_Framework_TestCase {

	/**
	 * Close mockery.
	 * 
	 * @return void
	 */
	public function tearDown()
	{
		m::close();
	}

	public function testFindingById()
	{
		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[createModel]');

		$query = m::mock('StdClass');
		$query->shouldReceive('newQuery')->andReturn($query);
		$query->shouldReceive('find')->with(1)->once()->andReturn('foo');

		$provider->shouldReceive('createModel')->once()->andReturn($query);

		$this->assertEquals('foo', $provider->findById(1));
	}

	/**
	 * @expectedException Cartalyst\Sentry\Users\UserNotFoundException
	 */
	public function testFailedFindingByIdThrowsException()
	{
		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[createModel]');

		$query = m::mock('StdClass');
		$query->shouldReceive('newQuery')->andReturn($query);
		$query->shouldReceive('find')->with(1)->once()->andReturn(null);

		$provider->shouldReceive('createModel')->once()->andReturn($query);

		$this->assertEquals('foo', $provider->findById(1));
	}

	public function testFindingByName()
	{
		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[createModel]');

		$loginColumn = 'email';

		$query = m::mock('StdClass');
		$query->shouldReceive('getLoginAttributeName')->once()->andReturn($loginColumn);

		$query->shouldReceive('newQuery')->andReturn($query);
		$query->shouldReceive('where')->with($loginColumn, '=', 'foo@bar.com')->once()->andReturn($query);
		$query->shouldReceive('first')->andReturn('bar');

		$provider->shouldReceive('createModel')->once()->andReturn($query);

		$this->assertEquals('bar', $provider->findByLogin('foo@bar.com'));
	}

	/**
	 * @expectedException Cartalyst\Sentry\Users\UserNotFoundException
	 */
	public function testFailedFindingByNameThrowsException()
	{
		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[createModel]');

		$loginColumn = 'email';

		$query = m::mock('StdClass');
		$query->shouldReceive('getLoginAttributeName')->once()->andReturn($loginColumn);

		$query->shouldReceive('newQuery')->andReturn($query);
		$query->shouldReceive('where')->with($loginColumn, '=', 'foo@bar.com')->once()->andReturn($query);
		$query->shouldReceive('first')->andReturn(null);

		$provider->shouldReceive('createModel')->once()->andReturn($query);

		$this->assertEquals('bar', $provider->findByLogin('foo@bar.com'));
	}

}