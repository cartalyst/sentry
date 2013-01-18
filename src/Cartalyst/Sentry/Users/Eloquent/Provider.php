<?php namespace Cartalyst\Sentry\Users\Eloquent;
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

use Cartalyst\Sentry\Hashing\HasherInterface;
use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Users\ProviderInterface;
use Cartalyst\Sentry\Users\UserInterface;
use Cartalyst\Sentry\Users\UserNotActivatedException;
use Cartalyst\Sentry\Users\UserNotFoundException;

class Provider implements ProviderInterface {

	/**
	 * The Eloquent user model.
	 *
	 * @var string
	 */
	protected $model = 'Cartalyst\Sentry\Users\Eloquent\User';

	/**
	 * The hasher for the model.
	 *
	 * @var Cartalyst\Sentry\Hashing\HasherInterface
	 */
	protected $hasher;

	/**
	 * Create a new Eloquent User provider.
	 *
	 * @param  Cartalyst\Sentry\Hashing\HasherInterface  $hasher
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
	 * Finds a user by the given user ID.
	 *
	 * @param  mixed  $id
	 * @return Cartalyst\Sentry\Users\UserInterface
	 * @throws Cartalyst\Sentry\Users\UserNotFoundException
	 */
	public function findById($id)
	{
		$model = $this->createModel();

		if ( ! $user = $model->newQuery()->find($id))
		{
			throw new UserNotFoundException("A user could not be found with ID [$id].");
		}

		$user->setHasher($this->hasher);

		return $user;
	}

	/**
	 * Finds a user by the login value.
	 *
	 * @param  string  $login
	 * @return Cartalyst\Sentry\Users\UserInterface
	 * @throws Cartalyst\Sentry\Users\UserNotFoundException
	 */
	public function findByLogin($login)
	{
		$model = $this->createModel();

		if ( ! $user = $model->newQuery()->where($model->getLoginName(), '=', $login)->first())
		{
			throw new UserNotFoundException("A user could not be found with a login value of [$login].");
		}

		$user->setHasher($this->hasher);

		return $user;
	}

	/**
	 * Finds a user by the given credentials.
	 *
	 * @param  array  $credentials
	 * @return Cartalyst\Sentry\Users\UserInterface
	 * @throws Cartalyst\Sentry\Users\UserNotFoundException
	 */
	public function findByCredentials(array $credentials)
	{
		$model = $this->createModel();

		if ( ! array_key_exists(($attribute = $model->getLoginName()), $credentials))
		{
			throw new \InvalidArgumentException("Login attribute [$attribute] was not provided.");
		}

		$query              = $model->newQuery();
		$hashableAttributes = $model->getHashableAttributes();
		$hashedCredentials  = array();

		// build query from given credentials
		foreach ($credentials as $credential => $value)
		{
			// Remove hashed attributes to check later as we need to check these
			// values after we retrieved them because of salts
			if (in_array($credential, $hashableAttributes))
			{
				$hashedCredentials = array_merge($hashedCredentials, array($credential => $value));
			}
			else
			{
				$query = $query->where($credential, '=', $value);
			}
		}

		if ( ! $user = $query->first())
		{
			throw new UserNotFoundException("A user was not found with the given credentials.");
		}

		// Now check the hashed credentials match ours
		foreach ($hashedCredentials as $credential => $value)
		{
			if ( ! $this->hasher->checkHash($value, $user->{$credential}))
			{
				throw new UserNotFoundException("A user was found to match all plain text credentials however hashed credential [$credential] did not match.");
			}
		}

		$user->setHasher($this->hasher);

		return $user;
	}

	/**
	 * Creates a user.
	 *
	 * @param  array  $credentials
	 * @return Cartalyst\Sentry\Users\UserInterface
	 */
	public function create(array $credentials)
	{
		$user = $this->createModel();
		$user->setHasher($this->hasher);
		$user->fill($credentials);
		$user->save();

		return $user;
	}

	/**
	 * Returns an empty user object.
	 *
	 * @return Cartalyst\Sentry\Users\UserInterface
	 */
	public function getEmptyUser()
	{
		$instance = $this->createModel();
		$instance->setHasher($this->hasher);
		return $instance;
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		$instance = new $class;
		return $instance;
	}

}
