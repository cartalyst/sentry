<?php namespace Cartalyst\Sentry\Throttling\Eloquent;
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

use Cartalyst\Sentry\Throttling\ThrottleInterface;

class Provider implements ProviderInterface {

	/**
	 * The Eloquent throttle model.
	 *
	 * @var string
	 */
	protected $model = 'Cartalyst\Sentry\Throttling\Eloquent\Throttle';

	/**
	 * Finds a throttler by the
	 * given user ID.
	 *
	 * @param  mixed  $id
	 * @return Cartalyst\Sentry\Throttling\ThrottleInterface
	 */
	public function findByUserId($id)
	{
		
	}

	/**
	 * Finds a throttling interface by the
	 * given user login.
	 *
	 * @param  string  $login
	 * @return Cartalyst\Sentry\Throttling\ThrottleInterface
	 */
	public function findByUserLogin($login)
	{
		
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