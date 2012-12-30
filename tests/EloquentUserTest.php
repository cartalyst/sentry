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

	public function setSuperUserAccessToEverything()
	{
		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User[getUserPermissions]');
		$user->shouldReceive('getUserPermissions')->andReturn(array(
			'superuser' => 1,
			'foo'       => -1,
		));

		$this->assertTrue($user->isSuperUser());
	}

	public function testGettingGroups()
	{
		$pivot = m::mock('StdClass');
		$pivot->shouldReceive('get')->once()->andReturn('foo');

		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User[groups]');
		$user->shouldReceive('groups')->once()->andReturn($pivot);

		$this->assertEquals('foo', $user->getGroups());
	}

	public function testInGroup()
	{
		$group1 = m::mock('Cartalyst\Sentry\Groups\GroupInterface');
		$group1->shouldReceive('getGroupId')->once()->andReturn(123);

		$group2 = m::mock('Cartalyst\Sentry\Groups\GroupInterface');
		$group2->shouldReceive('getGroupId')->once()->andReturn(124);

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[getGroups]');
		$user->shouldReceive('getGroups')->once()->andReturn(array($group2));

		$this->assertFalse($user->inGroup($group1));
	}

	public function testAddingToGroupChecksIfAlreadyInThatGroup()
	{
		$group = m::mock('Cartalyst\Sentry\Groups\GroupInterface');
		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User[inGroup,groups]');
		$user->shouldReceive('inGroup')->with($group)->once()->andReturn(true);
		$user->shouldReceive('groups')->never();

		$user->addGroup($group);
	}

	public function testAddingGroupAttachesToRelationship()
	{
		$group = m::mock('Cartalyst\Sentry\Groups\GroupInterface');

		$relationship = m::mock('StdClass');
		$relationship->shouldReceive('attach')->with($group)->once();

		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User[inGroup,groups]');
		$user->shouldReceive('inGroup')->once()->andReturn(false);
		$user->shouldReceive('groups')->once()->andReturn($relationship);

		$user->addGroup($group);
	}

	public function testRemovingFromGroupDetatchesRelationship()
	{
		$group = m::mock('Cartalyst\Sentry\Groups\GroupInterface');

		$relationship = m::mock('StdClass');
		$relationship->shouldReceive('detatch')->with($group)->once();

		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User[inGroup,groups]');
		$user->shouldReceive('inGroup')->once()->andReturn(true);
		$user->shouldReceive('groups')->once()->andReturn($relationship);

		$user->removeGroup($group);
	}

	public function testMergedPermissions()
	{
		$group1 = m::mock('Cartalyst\Sentry\Groups\GroupInterface');
		$group1->shouldReceive('getGroupPermissions')->once()->andReturn(array(
			'foo' => 1,
			'bar' => 1,
			'baz' => 1,
		));

		$group2 = m::mock('Cartalyst\Sentry\Groups\GroupInterface');
		$group2->shouldReceive('getGroupPermissions')->once()->andReturn(array(
			'qux' => 1,
		));

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[getGroups,getUserPermissions]');
		$user->shouldReceive('getGroups')->once()->andReturn(array($group1, $group2));
		$user->shouldReceive('getUserPermissions')->once()->andReturn(array(
			'corge' => 1,
			'foo'   => -1,
			'baz'   => -1,
		));

		$expected = array(
			'foo'   => -1,
			'bar'   => 1,
			'baz'   => -1,
			'qux'   => 1,
			'corge' => 1,
		);

		$this->assertEquals($expected, $user->getMergedPermissions());
	}

	public function testSuperUserHasAccessToEverything()
	{
		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User[isSuperUser]');
		$user->shouldReceive('isSuperUser')->once()->andReturn(true);

		$this->assertTrue($user->hasAccess('bar'));
	}

	public function testNormalUserPermissions()
	{
		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[isSuperUser,getMergedPermissions]');
		$user->shouldReceive('isSuperUser')->twice()->andReturn(false);
		$user->shouldReceive('getMergedPermissions')->twice()->andReturn(array(
			'foo' => -1,
			'bar' => 1,
			'baz' => 1,
		));

		$this->assertTrue($user->hasAccess('bar'));
		$this->assertFalse($user->hasAccess('foo'));
	}

}