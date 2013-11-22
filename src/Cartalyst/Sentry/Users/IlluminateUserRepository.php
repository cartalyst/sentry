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
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Cartalyst\Sentry\Hashing\HasherInterface;

class IlluminateUserRepository implements UserRepositoryInterface {

	/**
	 * Hasher.
	 *
	 * @var \Cartalyst\Sentry\Hashing\HasherInterface
	 */
	protected $hasher;

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * Create a new Illuminate user repository.
	 *
	 * @param  \Cartalyst\Sentry\Hashing\HasherInterface
	 */
	public function __construct(HasherInterface $hasher, $model)
	{
		$this->hasher = $hasher;
		$this->model = $model;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findById($id)
	{
		return $this->createModel()->newQuery()->find($id);
	}

	/**
	 * {@inheritDoc}
	 */
	public function findByCredentials(array $credentials)
	{
		$instance = $this->createModel();
		$loginNames = $instance->getLoginNames();
		$query = $instance->newQuery();

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

		$user = $query->first();

		if ($user and $this->validateCredentials($user, $password))
		{
			return $user;
		}
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
	 * Parses the given credentials to return logins, password and others.
	 *
	 * @param  array  $credentials
	 * @param  array  $loginNames
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	protected function parseCredentials(array $credentials, array $loginNames)
	{
		if ( ! array_key_exists('password', $credentials))
		{
			throw new \InvalidArgumentException('You have not passed a [password].');
		}

		$passedNames = array_intersect_key($credentials, array_flip($loginNames));

		if (count($passedNames) > 0)
		{
			$logins = array();

			foreach ($passedNames as $name => $value)
			{
				$logins[$name] = $credentials[$name];
				unset($credentials[$name]);
			}
		}
		elseif (isset($credentials['login']))
		{
			$logins = $credentials['login'];
			unset($credentials['login']);
		}
		else
		{
			throw new \InvalidArgumentException('No [login] credential was passed.');
		}

		$password = $credentials['password'];
		unset($credentials['password']);
		return array($logins, $password, $credentials);
	}

	/**
	 * Validates the given password against a user.
	 *
	 * @param  \Cartalyst\Sentry\Users\EloquentUser  $user
	 * @return bool
	 */
	protected function validateCredentials(EloquentUser $user, $password)
	{
		return $this->hasher->checkHash($password, $user->password);
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}
