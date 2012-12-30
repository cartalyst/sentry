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

	public function testInGroup()
	{
		$provider = new Provider(m::mock('Cartalyst\Sentry\Hashing\HasherInterface'));

		$group1 = m::mock('Cartalyst\Sentry\Groups\GroupInterface');
		$group1->shouldReceive('getGroupId')->times(3)->andReturn(123);

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User');
		$user->shouldReceive('getGroups')->once()->andReturn(array($group1));
		$this->assertTrue($provider->inGroup($user, $group1));

		$group2 = m::mock('Cartalyst\Sentry\Groups\GroupInterface');
		$group2->shouldReceive('getGroupId')->once()->andReturn(124);

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User');
		$user->shouldReceive('getGroups')->once()->andReturn(array($group2));
		$this->assertFalse($provider->inGroup($user, $group1));
	}

	public function testAddingToGroupChecksIfAlreadyInThatGroup()
	{
		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User');
		$group = m::mock('Cartalyst\Sentry\Groups\GroupInterface');

		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[inGroup]');
		$provider->shouldReceive('inGroup')->with($user, $group)->once()->andReturn(true);

		$this->assertNull($provider->addGroup($user, $group));
	}

	public function testAddingGroupAttachesToRelationship()
	{
		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User');
		$user->shouldReceive('getGroups')->once()->andReturn(array());

		$group = m::mock('Cartalyst\Sentry\Groups\GroupInterface');

		$groups = m::mock('StdClass');
		$groups->shouldReceive('attach')->with($group)->once();

		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[groups]');
		$provider->shouldReceive('groups')->once()->andReturn($groups);

		$this->assertNull($provider->addGroup($user, $group));
	}

	public function testRemovingFromGroupReturnsTrueIfNotInThatGruop()
	{
		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User');
		$group = m::mock('Cartalyst\Sentry\Groups\GroupInterface');

		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[inGroup]');
		$provider->shouldReceive('inGroup')->with($user, $group)->once()->andReturn(false);

		$this->assertNull($provider->removeGroup($user, $group));
	}

	public function testRemovingFromGroupDetatchesRelationship()
	{
		$group = m::mock('Cartalyst\Sentry\Groups\GroupInterface');
		$group->shouldReceive('getGroupId')->twice()->andReturn(123);

		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User');
		$user->shouldReceive('getGroups')->once()->andReturn(array($group));

		$groups = m::mock('StdClass');
		$groups->shouldReceive('detatch')->with($group)->once();

		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[groups]');
		$provider->shouldReceive('groups')->once()->andReturn($groups);

		$this->assertNull($provider->removeGroup($user, $group));
	}

	public function testGettingGroups()
	{
		$pivot = m::mock('StdClass');
		$pivot->shouldReceive('get')->once()->andReturn('foo');

		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User[groups]');
		$user->shouldReceive('groups')->once()->andReturn($pivot);

		$provider = new Provider(m::mock('Cartalyst\Sentry\Hashing\HasherInterface'));
		$this->assertEquals('foo', $provider->getGroups($user));
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

		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User');
		$user->shouldReceive('getUserPermissions')->once()->andReturn(array(
			'corge' => 1,
			'foo'   => -1,
			'baz'   => -1,
		));

		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[getGroups]');
		$provider->shouldReceive('getGroups')->with($user)->once()->andReturn(array($group1, $group2));

		$expected = array(
			'foo'   => -1,
			'bar'   => 1,
			'baz'   => -1,
			'qux'   => 1,
			'corge' => 1,
		);

		$this->assertEquals($expected, $provider->getMergedPermissions($user));
	}

	public function testSuperUserHasAccessToEverything()
	{
		$user  = m::mock('Cartalyst\Sentry\Users\Eloquent\User[isSuperUser]');
		$user->shouldReceive('isSuperUser')->once()->andReturn(true);

		$provider = new Provider(m::mock('Cartalyst\Sentry\Hashing\HasherInterface'));
		$this->assertTrue($provider->hasAccess($user, 'bar'));
	}

	public function testNormalUserPermissions()
	{
		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[isSuperUser]');
		$user->shouldReceive('isSuperUser')->twice()->andReturn(false);

		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[getMergedPermissions]');
		$provider->shouldReceive('getMergedPermissions')->with($user)->twice()->andReturn(array(
			'foo' => -1,
			'bar' => 1,
			'baz' => 1,
		));

		$this->assertTrue($provider->hasAccess($user, 'bar'));
		$this->assertFalse($provider->hasAccess($user, 'foo'));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testFindingByCredentialsFailsWithoutLoginColumn()
	{
		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User');
		$user->shouldReceive('getLoginAttributeName')->once()->andReturn('foo');

		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[createModel]');
		$provider->shouldReceive('createModel')->once()->andReturn($user);

		$provider->findByCredentials(array(
			'not_foo' => 'ff',
		));
	}

	public function testFindingByCredentialsFailsWhenModelIsNull()
	{
		$query = m::mock('StdClass');
		$query->shouldReceive('where')->with('foo', '=', 'fooval')->once()->andReturn($query);
		$query->shouldReceive('where')->with('bar', '=', 'barval')->once()->andReturn($query);
		$query->shouldReceive('first')->andReturn(null);

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User');
		$user->shouldReceive('getLoginAttributeName')->once()->andReturn('foo');
		$user->shouldReceive('newQuery')->andReturn($query);

		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[createModel,getHashableCredentials]');
		$provider->shouldReceive('createModel')->once()->andReturn($user);
		$provider->shouldReceive('getHashableCredentials')->once()->andReturn(array('baz', 'bat'));

		$result = $provider->findByCredentials(array(
			'foo' => 'fooval',
			'bar' => 'barval',
			'baz' => 'unhashed_baz',
			'bat' => 'unhashed_bat',
		));

		$this->assertNull($result);
	}

	public function testFindingByCredentials()
	{
		$actualUser = m::mock('Cartalyst\Sentry\Users\Eloquent\User');
		$actualUser->shouldReceive('getAttribute')->with('baz')->andReturn('hashed_baz');
		$actualUser->shouldReceive('getAttribute')->with('bat')->andReturn('hashed_bat');

		$hasher = m::mock('Cartalyst\Sentry\Hashing\HasherInterface');
		$hasher->shouldReceive('checkhash')->with('unhashed_baz', 'hashed_baz')->
		once()->andReturn(true);
		$hasher->shouldReceive('checkhash')->with('unhashed_bat', 'hashed_bat')->once()->andReturn(true);

		$query = m::mock('StdClass');
		$query->shouldReceive('where')->with('foo', '=', 'fooval')->once()->andReturn($query);
		$query->shouldReceive('where')->with('bar', '=', 'barval')->once()->andReturn($query);
		$query->shouldReceive('first')->andReturn($actualUser);

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User');
		$user->shouldReceive('getLoginAttributeName')->once()->andReturn('foo');
		$user->shouldReceive('newQuery')->andReturn($query);

		$provider = m::mock('Cartalyst\Sentry\Users\Eloquent\Provider[createModel,getHashableCredentials]');
		$provider->__construct($hasher);

		$provider->shouldReceive('createModel')->once()->andReturn($user);
		$provider->shouldReceive('getHashableCredentials')->once()->andReturn(array('baz', 'bat'));

		$result = $provider->findByCredentials(array(
			'foo' => 'fooval',
			'bar' => 'barval',
			'baz' => 'unhashed_baz',
			'bat' => 'unhashed_bat',
		));

		$this->assertEquals($actualUser, $result);
	}

}