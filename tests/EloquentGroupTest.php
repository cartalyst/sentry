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
use Cartalyst\Sentry\Groups\Eloquent\Group;

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

	public function testGroupId()
	{
		$group = new Group;
		$group->id = 123;

		$this->assertEquals(123, $group->getGroupId());
	}

	public function testGroupName()
	{
		$group = new Group;
		$group->name = 'foo';

		$this->assertEquals('foo', $group->getGroupName());
	}

	public function testSettingPermissions()
	{
		$permissions = array(
			'foo' => 1,
			'bar' => 1,
			'baz' => 1,
			'qux' => 1,
		);

		$group = new Group;

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

		$group = new Group;

		$expected = '{"foo":1,"bar":1,"qux":1}';

		$this->assertEquals($expected, $group->setPermissions($permissions));
	}

	public function testPermissionsAreMergedAndRemovedProperly()
	{
		$group = new Group;
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
		$group = new Group;
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

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testExceptionIsThrownForInvalidPermissionsDecoding()
	{
		$json = '{"foo":1,"bar:1';
		$group = new Group;

		$group->getPermissions($json);
	}

}