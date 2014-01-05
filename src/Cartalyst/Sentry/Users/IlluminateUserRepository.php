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
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Carbon\Carbon;
use Cartalyst\Sentry\Hashing\HasherInterface;
use Closure;

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
	protected $model = 'Cartalyst\Sentry\Users\EloquentUser';

	/**
	 * Create a new Illuminate user repository.
	 *
	 * @param  \Cartalyst\Sentry\Hashing\HasherInterface  $hasher
	 * @param  string  $model
	 * @return void
	 */
	public function __construct(HasherInterface $hasher, $model = null)
	{
		$this->hasher = $hasher;

		if (isset($model))
		{
			$this->model = $model;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function findAll()
	{
		return $this->createModel()
			->newQuery()
			->with('groups')
			->get();
	}

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

		if ($user and $this->validateCredentials($user, $password))
		{
			return $user;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function validateCredentials(UserInterface $user, array $credentials)
	{
		return $this->hasher->check($credentials['password'], $user->password);
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
		return $user->save();
	}

	/**
	 * {@inheritDoc}
	 */
	public function recordLogout(UserInterface $user)
	{
		return $user->save();
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForCreation(array $credentials)
	{
		return $this->validateUser($credentials);
	}

	/**
	 * {@inheritDoc}
	 */
	public function validForUpdate($user, array $credentials)
	{
		if ($user instanceof UserInterface)
		{
			$user = $user->getUserId();
		}

		return $this->validateUser($credentials, $user);
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(array $credentials, Closure $callback = null)
	{
		$user = $this->createModel();
		$this->fill($user, $credentials);

		if ($callback)
		{
			$result = $callback($user);

			if ($result === false)
			{
				return false;
			}
		}

		$user->save();

		return $user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update($user, array $credentials)
	{
		if ( ! $user instanceof UserInterface)
		{
			$user = $this->findById($user);
		}

		$this->fill($user, $credentials);
		$user->save();

		return $user;
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
		if (isset($credentials['password']))
		{
			$password = $credentials['password'];
			unset($credentials['password']);
		}
		else
		{
			$password = null;
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
			$logins = array();
		}

		return array($logins, $password, $credentials);
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

	/**
	 * Validates the user.
	 *
	 * @param  array  $credentials
	 * @param  int  $id
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	protected function validateUser(array $credentials, $id = null)
	{
		$instance = $this->createModel();
		$loginNames = $instance->getLoginNames();

		// We will simply parse credentials which checks logins and passwords
		list($logins, $password, $credentials) = $this->parseCredentials($credentials, $loginNames);

		if ($id === null)
		{
			if (empty($logins))
			{
				throw new \InvalidArgumentException('No [login] credential was passed.');
			}
			if ($password === null)
			{
				throw new \InvalidArgumentException('You have not passed a [password].');
			}
		}

		if ($password and strlen($password) < 6)
		{
			throw new \InvalidArgumentException('Your [password] must be at least 6 characters.');
		}

		return true;
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

	/**
	 * Runtime override of the model.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function setModel($model)
	{
		$this->model = $model;
	}

}
