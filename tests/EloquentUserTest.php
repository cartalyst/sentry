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
use Cartalyst\Sentry\Users\Eloquent\User;

class EloquentUserTest extends PHPUnit_Framework_TestCase {

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

	public function testUserIdCallsKey()
	{
		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[getKey]');
		$user->shouldReceive('getKey')->once()->andReturn('foo');

		$this->assertEquals('foo', $user->getUserId());
	}

	public function testUserLoginCallsLoginColumn()
	{
		$user = new User;
		$user->email = 'foo@bar.com';

		$this->assertEquals('foo@bar.com', $user->getUserLogin());
	}

	public function testUserPassowrdCallsPassowrdColumn()
	{
		$user = new User;
		$user->password = 'hashed_password_here';

		$this->assertEquals('hashed_password_here', $user->getUserPassword());
	}

}