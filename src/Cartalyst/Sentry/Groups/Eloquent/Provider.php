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

use Cartalyst\Sentry\Groups\GroupInterface;
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
		return $model->newQuery()->find($id);
	}

	/**
	 * Find group by name.
	 *
	 * @param  string  $name
	 * @return Cartalyst\Sentry\GroupInterface  $group
	 */
	public function findByName($name)
	{
		$model = $this->createModel();
		return $model->newQuery()->where('name', '=', $name)->first();
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}