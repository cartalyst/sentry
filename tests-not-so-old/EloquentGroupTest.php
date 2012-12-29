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
use Cartalyst\Sentry\Groups\EloquentGroup;

class EloquentGroupTest extends PHPUnit_Framework_TestCase {

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

	public function testFindingById()
	{
		$modelGroup = m::mock('Catalyst\Sentry\Groups\EloquentGroup');

		$object = m::mock('StdClass');
		$object->shouldReceive('first')->once()->andReturn($modelGroup);

		$providerGroup = m::mock('Cartalyst\Sentry\Groups\EloquentGroup[where,first]');
		$providerGroup->shouldReceive('where')->with($providerGroup->getKeyName(), '=', 1)->once()->andReturn($object);

		$this->assertEquals($modelGroup, $providerGroup->findById(1));
	}

	/**
	 * @expectedException Cartalyst\Sentry\Groups\GroupNotFoundException
	 */
	public function testFailedFindingByIdThrowsException()
	{
		$object = m::mock('StdClass');
		$object->shouldReceive('first')->once()->andReturn(null);

		$providerGroup = m::mock('Cartalyst\Sentry\Groups\EloquentGroup[where,first]');
		$providerGroup->shouldReceive('where')->with($providerGroup->getKeyName(), '=', 1)->once()->andReturn($object);

		$providerGroup->findById(1);
	}

	public function testFindingByName()
	{
		$modelGroup = m::mock('Catalyst\Sentry\Groups\EloquentGroup');

		$object = m::mock('StdClass');
		$object->shouldReceive('first')->once()->andReturn($modelGroup);

		$providerGroup = m::mock('Cartalyst\Sentry\Groups\EloquentGroup[where,first]');
		$providerGroup->shouldReceive('where')->with('name', '=', 'foo')->once()->andReturn($object);

		$this->assertEquals($modelGroup, $providerGroup->findByName('foo'));
	}

	/**
	 * @expectedException Cartalyst\Sentry\Groups\GroupNotFoundException
	 */
	public function testFailedFindingByNameThrowsException()
	{
		$object = m::mock('StdClass');
		$object->shouldReceive('first')->once()->andReturn(null);

		$providerGroup = m::mock('Cartalyst\Sentry\Groups\EloquentGroup[where,first]');
		$providerGroup->shouldReceive('where')->with('name', '=', 'foo')->once()->andReturn($object);

		$providerGroup->findByName('foo');
	}

	/**
	 * @expectedException Cartalyst\Sentry\Groups\NameFieldRequiredException
	 */
	public function testValidationThrowsExceptionForMissingName()
	{
		$group = new EloquentGroup;
		$group->validate(array());
	}

	/**
	 * @expectedException Cartalyst\Sentry\Groups\GroupExistsException
	 */
	public function testValidationThrowsExceptionForDuplicateNameOnNonExistent()
	{
		$persistedGroup = m::mock('StdClass');
		$persistedGroup->id   = 123;
		$persistedGroup->name = 'foo';

		$originalGroup = m::mock('Cartalyst\Sentry\Groups\EloquentGroup[findByName]');
		$originalGroup->name = 'foo';
		$originalGroup->shouldReceive('findByName')->with('foo')->once()->andReturn($persistedGroup);

		$group = new EloquentGroup;
		$group->validate(array(
			'name' => 'foo'
		));

		$originalGroup->validate();
	}

	/**
	 * @expectedException Cartalyst\Sentry\Groups\GroupExistsException
	 */
	public function testValidationThrowsExceptionForDuplicateNameOnExistent()
	{
		$persistedGroup = m::mock('StdClass');
		$persistedGroup->id   = 123;
		$persistedGroup->name = 'foo';

		$originalGroup = m::mock('Cartalyst\Sentry\Groups\EloquentGroup[findByName]');
		$originalGroup->name = 124;
		$originalGroup->name = 'foo';
		$originalGroup->shouldReceive('findByName')->with('foo')->once()->andReturn($persistedGroup);

		$originalGroup->validate();
	}

	public function testSettingPermissions()
	{
		$permissions = array(
			'foo' => 1,
			'bar' => 1,
			'baz' => 1,
			'qux' => 1,
		);

		$group = new EloquentGroup;

		$expected = '{"foo":1,"bar":1,"baz":1,"qux":1}';

		$this->assertEquals($expected, $group->setPermissions($permissions));
	}

	public function testSettingPermissionsWhenSomeAreSetTo0()
	{
		$permissions = array(
			'foo' => 1,
			'bar' => 1,
			'baz' => 0,
			'qux' => 1,
		);

		$group = new EloquentGroup;

		$expected = '{"foo":1,"bar":1,"qux":1}';

		$this->assertEquals($expected, $group->setPermissions($permissions));
	}

	public function testPermissionsAreMergedAndRemovedProperly()
	{
		$group = new EloquentGroup;
		$group->permissions = array(
			'foo' => 1,
			'bar' => 1,
		);

		$group->permissions = array(
			'baz' => 1,
			'qux' => 1,
			'foo' => 0,
		);

		$expected = array(
			'bar' => 1,
			'baz' => 1,
			'qux' => 1,
		);

		$this->assertEquals($expected, $group->permissions);
	}

	public function testPermissionsAreCastAsAnArrayWhenTheModelIs()
	{
		$group = new EloquentGroup;
		$group->name = 'foo';
		$group->permissions = array(
			'bar' => 1,
			'baz' => 1,
			'qux' => 1,
		);

		$expected = array(
			'name' => 'foo',
			'permissions' => array(
				'bar' => 1,
				'baz' => 1,
				'qux' => 1,
			),
		);

		$this->assertEquals($expected, $group->toArray());
	}

}