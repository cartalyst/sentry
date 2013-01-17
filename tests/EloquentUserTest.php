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

	public function testUserLoginCallsLoginAttribute()
	{
		$user = new User;
		$user->email = 'foo@bar.com';

		$this->assertEquals('foo@bar.com', $user->getUserLogin());
	}

	public function testUserLoginNameCallsLoginName()
	{
		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[getLoginName]');
		$user->shouldReceive('getLoginName')->once()->andReturn('foo');
		$this->assertEquals('foo', $user->getUserLoginName());
	}

	public function testUserPassowrdCallsPasswordAttribute()
	{
		$hasher = m::mock('Cartalyst\Sentry\Hashing\HasherInterface');
		$hasher->shouldReceive('hash')->with('unhashed_password_here')->once()->andReturn('hashed_password_here');
		$user = new User;
		$user->setHasher($hasher);
		$user->password = 'unhashed_password_here';

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

	public function testRemovingFromGroupDetachesRelationship()
	{
		$group = m::mock('Cartalyst\Sentry\Groups\GroupInterface');

		$relationship = m::mock('StdClass');
		$relationship->shouldReceive('detach')->with($group)->once();

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

	/**
	 * @expectedException Cartalyst\Sentry\Users\LoginRequiredException
	 */
	public function testValidationThrowsLoginExceptionIfNoneGiven()
	{
		$user = new User;
		$user->validate();
	}

	/**
	 * @expectedException Cartalyst\Sentry\Users\PasswordRequiredException
	 */
	public function testValidationThrowsPasswordExceptionIfNoneGiven()
	{
		$user = new User;
		$user->email = 'foo';
		$user->validate();
	}

	/**
	 * @expectedException Cartalyst\Sentry\Users\UserExistsException
	 */
	public function testValidationFailsWhenUserAlreadyExists()
	{
		$hasher = m::mock('Cartalyst\Sentry\Hashing\HasherInterface');
		$hasher->shouldReceive('hash')->with('bazbat')->once()->andReturn('hashed_bazbat');

		$persistedUser = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$persistedUser->shouldReceive('getUserId')->once()->andReturn(123);

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[newQuery]');
		$user->setHasher($hasher);
		$user->email = 'foo@bar.com';
		$user->password = 'bazbat';

		$query = m::mock('StdClass');
		$query->shouldReceive('where')->with('email', '=', 'foo@bar.com')->once()->andReturn($query);
		$query->shouldReceive('first')->once()->andReturn($persistedUser);

		$user->shouldReceive('newQuery')->once()->andReturn($query);

		$user->validate();
	}

	/**
	 * @expectedException Cartalyst\Sentry\Users\UserExistsException
	 */
	public function testValidationFailsWhenUserAlreadyExistsOnExistent()
	{
		$hasher = m::mock('Cartalyst\Sentry\Hashing\HasherInterface');
		$hasher->shouldReceive('hash')->with('bazbat')->once()->andReturn('hashed_bazbat');

		$persistedUser = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$persistedUser->shouldReceive('getUserId')->once()->andReturn(123);

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[newQuery]');
		$user->setHasher($hasher);
		$user->id = 124;
		$user->email = 'foo@bar.com';
		$user->password = 'bazbat';

		$query = m::mock('StdClass');
		$query->shouldReceive('where')->with('email', '=', 'foo@bar.com')->once()->andReturn($query);
		$query->shouldReceive('first')->once()->andReturn($persistedUser);

		$user->shouldReceive('newQuery')->once()->andReturn($query);

		$user->validate();
	}

	public function testValidationDoesNotThrowAnExceptionIfPersistedUserIsThisUser()
	{
		$hasher = m::mock('Cartalyst\Sentry\Hashing\HasherInterface');
		$hasher->shouldReceive('hash')->with('bazbat')->once()->andReturn('hashed_bazbat');

		$persistedUser = m::mock('Cartalyst\Sentry\Users\UserInterface');
		$persistedUser->shouldReceive('getUserId')->once()->andReturn(123);

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[newQuery]');
		$user->setHasher($hasher);
		$user->id = 123;
		$user->email = 'foo@bar.com';
		$user->password = 'bazbat';

		$query = m::mock('StdClass');
		$query->shouldReceive('where')->with('email', '=', 'foo@bar.com')->once()->andReturn($query);
		$query->shouldReceive('first')->once()->andReturn($persistedUser);

		$user->shouldReceive('newQuery')->once()->andReturn($query);

		$this->assertTrue($user->validate());
	}

	public function testClearResetPassword()
	{
		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[save]');
		$user->shouldReceive('save')->never();
		$user->clearResetPassword();

		$hasher = m::mock('Cartalyst\Sentry\Hashing\HasherInterface');
		$hasher->shouldReceive('hash')->with('foo_bar_baz')->once()->andReturn('hashed_foo_bar_baz');

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[save]');
		$user->setHasher($hasher);

		$user->reset_password_hash = 'foo_bar_baz';
		$user->shouldReceive('save')->once();
		$user->clearResetPassword();
		$this->assertNull($user->reset_password_hash);
	}

	public function testHasherSettingAndGetting()
	{
		$hasher = m::mock('Cartalyst\Sentry\Hashing\HasherInterface');
		$user = new User;
		$this->assertNull($user->getHasher());
		$user->setHasher($hasher);
		$this->assertEquals($hasher, $user->getHasher());
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testHasherThrowsExceptionIfNotSet()
	{
		$user = new User;
		$user->checkHash('foo', 'bar');
	}

	public function testRandomStrings()
	{
		$user = new User;
		$last = '';

		for ($i = 0; $i < 500; $i++)
		{
			$now = $user->getRandomString();

			if ($now === $last)
			{
				throw new \UnexpectedValueException("Two random strings are the same, [$now], [$last].");
			}

			$last = $now;
		}

	}

	public function testGetActivationCode()
	{
		$randomString = 'random_string_here';
		$hashedRandomString = 'hashed_random_string_here';

		$hasher = m::mock('Cartalyst\Sentry\Hashing\HasherInterface');
		$hasher->shouldReceive('hash')->with($randomString)->once()->andReturn($hashedRandomString);

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[save,getRandomString]');
		$user->setHasher($hasher);

		$this->assertNull($user->activation_hash);
		$user->shouldReceive('save')->once();
		$user->shouldReceive('getRandomString')->once()->andReturn($randomString);

		$activationCode = $user->getActivationCode();
		$this->assertEquals($hashedRandomString, $activationCode);
		$this->assertEquals($hashedRandomString, $user->activation_hash);
	}

	public function testGetResetPasswordCode()
	{
		$randomString = 'random_string_here';
		$hashedRandomString = 'hashed_random_string_here';

		$hasher = m::mock('Cartalyst\Sentry\Hashing\HasherInterface');
		$hasher->shouldReceive('hash')->with($randomString)->once()->andReturn($hashedRandomString);

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[save,getRandomString]');
		$user->setHasher($hasher);

		$this->assertNull($user->reset_password_hash);
		$user->shouldReceive('save')->once();
		$user->shouldReceive('getRandomString')->once()->andReturn($randomString);

		$resetCode = $user->getResetPasswordCode();
		$this->assertEquals($hashedRandomString, $resetCode);
		$this->assertEquals($hashedRandomString, $user->reset_password_hash);
	}

	public function testUserIsNotActivatedTwice()
	{
		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[checkHash]');
		$user->shouldReceive('checkHash')->never();
		$user->activated = true;

		$this->assertTrue($user->attemptActivation('not_needed'));
	}

	public function testUserActivation()
	{
		$hasher = m::mock('Cartalyst\Sentry\Hashing\HasherInterface');
		$hasher->shouldReceive('hash')->with('activation_code')->once()->andReturn('activation_code');

		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[getActivationCode,checkHash,save]');
		$user->setHasher($hasher);

		$user->activation_hash = 'activation_code';
		$user->shouldReceive('save')->once()->andReturn(true);

		$this->assertTrue($user->attemptActivation('activation_code'));
		$this->assertNull($user->activation_hash);
		$this->assertTrue($user->activated);
	}

	public function testCheckingResetPasswordCode()
	{
		$user = new User;
		$user->setHasher($hasher = m::mock('Cartalyst\Sentry\Hashing\HasherInterface'));

		// Create a new hash
		$hasher->shouldReceive('hash')->with('reset_code')->andReturn('hashed_reset_code');
		$user->reset_password_hash = 'reset_code';

		// Check the hash
		$this->assertTrue($user->checkResetPasswordCode('hashed_reset_code'));
		$this->assertFalse($user->checkResetPasswordCode('not_the_hashed_reset_code'));
	}

	public function testResettingPassword()
	{
		$user = m::mock('Cartalyst\Sentry\Users\Eloquent\User[checkResetPasswordCode,save]');
		$user->shouldReceive('checkResetPasswordCode')->with('reset_code')->andReturn(true);
		$user->setHasher($hasher = m::mock('Cartalyst\Sentry\Hashing\HasherInterface'));

		$hasher->shouldReceive('hash')->with('new_password')->once()->andReturn('hashed_new_password');

		$user->shouldReceive('save')->once()->andReturn(true);

		$this->assertTrue($user->attemptResetPassword('reset_code', 'new_password'));
		$this->assertNull($user->reset_password_hash);
		$this->assertEquals('hashed_new_password', $user->getUserPassword());
	}

	public function testPermissionsAreMergedAndRemovedProperly()
	{
		$user = new User;
		$user->permissions = array(
			'foo' => 1,
			'bar' => 1,
		);

		$user->permissions = array(
			'baz' => 1,
			'qux' => 1,
			'foo' => 0,
		);

		$expected = array(
			'bar' => 1,
			'baz' => 1,
			'qux' => 1,
		);

		$this->assertEquals($expected, $user->permissions);
	}


	public function testPermissionsWithArrayCastingAndJsonCasting()
	{
		$user = new User;
		$user->email = 'foo@bar.com';
		$user->permissions = array(
			'foo' => 1,
			'bar' => -1,
			'baz' => 1,
		);

		$expected = array(
			'email' => 'foo@bar.com',
			'permissions' => array(
				'foo' => 1,
				'bar' => -1,
				'baz' => 1,
			),
		);
		$this->assertEquals($expected, $user->toArray());

		$expected = '{"email":"foo@bar.com","permissions":{"foo":1,"bar":-1,"baz":1}}';
		$this->assertEquals($expected, (string) $user);
	}

}
