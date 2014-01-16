<?php namespace Cartalyst\Sentry\Users;
/**
 * Part of the Sentry package.
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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Carbon\Carbon;

class IlluminateUserRepository extends BaseUserRepository implements UserRepositoryInterface {

	/**
	 * {@inheritDoc}
	 */
	protected $model = 'Cartalyst\Sentry\Users\EloquentUser';

	/**
	 * {@inheritDoc}
	 */
	public function findById($id)
	{
		return $this
			->createModel()
			->newQuery()
			->with('groups')
			->find($id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function findByCredentials(array $credentials)
	{
		$instance = $this->createModel();
		$loginNames = $instance->getLoginNames();
		$query = $instance->newQuery()->with(array('groups'));

		list($logins, $password, $credentials) = $this->parseCredentials($credentials, $loginNames);

		if (is_array($logins))
		{
			foreach ($logins as $key => $value)
			{
				$query->where($key, $value);
			}
		}
		else
		{
			$query->whereNested(function($query) use ($loginNames, $logins)
			{
				foreach ($loginNames as $name)
				{
					$query->orWhere($name, $logins);
				}
			});
		}

		return $query->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findByPersistenceCode($code)
	{
		// Narrow down our query to those who's persistence codes array
		// contains ours. We'll filter the right user out.
		$users = $this->createModel()
			->newQuery()
			->with(array('groups'))
			->where('persistence_codes', 'like', "%{$code}%")
			->get();

		$users = $users->filter(function($user) use ($code)
		{
			return in_array($code, $user->persistence_codes);
		});

		if (count($users) > 1)
		{
			throw new \RuntimeException('Multiple users were found with the same persistence code. This should not happen.');
		}

		return $users->first();
	}

	/**
	 * {@inheritDoc}
	 */
	public function recordLogin(UserInterface $user)
	{
		$user->last_login = Carbon::now();
		return $user->save() ? $user : false;
	}

	/**
	 * Fills a user with the given credentials, intelligently.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserInterface  $user
	 * @param  array  $credentials
	 * @return void
	 */
	protected function fill(UserInterface $user, array $credentials)
	{
		$loginNames = $user->getLoginNames();

		list($logins, $password, $credentials) = $this->parseCredentials($credentials, $loginNames);

		if (is_array($logins))
		{
			$user->fill($logins);
		}
		else
		{
			$loginName = reset($loginNames);
			$user->fill(array(
				$loginName => $logins,
			));
		}

		$user->fill($credentials);

		if (isset($password))
		{
			$password = $this->hasher->hash($password);
			$user->fill(compact('password'));
		}
	}

}
