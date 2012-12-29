<?php namespace Cartalyst\Sentry\Groups\Eloquent;
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

use Cartalyst\Sentry\Groups\GroupExistsException;
use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Groups\GroupNotFoundException;
use Cartalyst\Sentry\Groups\NameFieldRequiredException;
use Cartalyst\Sentry\Groups\ProviderInterface;

class Provider implements ProviderInterface {

	/**
	 * The Eloquent group model.
	 *
	 * @var string
	 */
	protected $model = 'Cartalyst\Sentry\Groups\Eloquent\Group';

	/**
	 * Create a new Eloquent Group provider.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function __construct($model = null)
	{
		if (isset($model))
		{
			$this->model = $model;
		}
	}

	/**
	 * Find group by ID.
	 *
	 * @param  int  $id
	 * @return Cartalyst\Sentry\GroupInterface  $group
	 * @throws Cartalyst\Sentry\GroupNotFoundException
	 */
	public function findById($id)
	{
		$model = $this->createModel();

		if ( ! $group = $model->newQuery()->find($id))
		{
			throw new GroupNotFoundException;
		}

		return $group;
	}

	/**
	 * Find group by name.
	 *
	 * @param  string  $name
	 * @return Cartalyst\Sentry\GroupInterface  $group
	 * @throws Cartalyst\Sentry\GroupNotFoundException
	 */
	public function findByName($name)
	{
		$model = $this->createModel();

		if ( ! $group = $model->newQuery()->where('name', '=', $name)->first())
		{
			throw new GroupNotFoundException;
		}

		return $group;
	}

	/**
	 * Validates the group and throws a number of
	 * Exceptions if validation fails.
	 *
	 * @param  Cartalyst\Sentry\Groups\GroupInterface  $group
	 * @return bool
	 * @throws Cartalyst\Sentry\Groups\NameFieldRequiredException
	 * @throws Cartalyst\Sentry\Groups\GroupExistsException
	 */
	public function validate(GroupInterface $group)
	{
		$name = $group->getGroupName();

		// Check if name field was passed
		if ( ! $name)
		{
			throw new NameFieldRequiredException;
		}

		// Check if group already exists
		try
		{
			$persistedGroup = $this->findByName($name);
		}
		catch (GroupNotFoundException $e)
		{
			$persistedGroup = null;
		}

		if ($persistedGroup and $persistedGroup->getGroupId() != $group->getGroupId())
		{
			throw new GroupExistsException;
		}

		return true;
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class();
	}

}