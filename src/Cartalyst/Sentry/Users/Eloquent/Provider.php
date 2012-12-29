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

use Cartalyst\Sentry\Users\ProviderInterface;

class Provider implements ProviderInterface {

	/**
	 * Get user login column
	 *
	 * @return string
	 */
	public function getLoginColumn()
	{
		return $this->loginColumn;
	}

	/**
	 * Get user login column
	 *
	 * @param  int  $id
	 * @return Cartalyst\Sentry\UserInterface
	 */
	public function findById($id)
	{
		$user = $this->find($id);

		if ( ! $user)
		{
			throw new UserNotFoundException;
		}

		return $user;
	}

	/**
	 * Get user by login value
	 *
	 * @param  string  $login
	 * @return Cartalyst\Sentry\UserInterface
	 */
	public function findByLogin($login)
	{
		$user = $this->where($this->loginColumn, '=', $login)->first();

		if ( ! $user)
		{
			throw new UserNotFoundException;
		}

		return $user;
	}

	/**
	 * Get user by credentials
	 *
	 * @param  string  $login
	 * @param  string  $password
	 * @return Cartalyst\Sentry\UserInterface
	 */
	public function findByCredentials(array $credentials)
	{
		if ( ! array_key_exists($this->loginColumn, $credentials))
		{
			throw new LoginFieldRequiredException;
		}

		$query = $this->newQuery();
		$hashedCredentials = array();

		// build query from given credentials
		foreach ($credentials as $credential => $value)
		{
			// remove hashed attributes to check later as we need to check these
			// values after we retrieved them because of salts
			if (in_array($credential, $this->hashedFields))
			{
				$hashedCredentials += array($credential => $value);
				continue;
			}

			$query = $query->where($credential, '=', $value);
		}

		// retrieve the user
		$user = $query->first();

		if ( ! $user)
		{
			throw new UserNotFoundException;
		}

		// now we check for hashed values to make sure they match as well
		foreach ($hashedCredentials as $credential => $value)
		{
			if ( ! $this->checkHash($value, $user->{$credential}))
			{
				throw new UserNotFoundException;
			}
		}

		return $user;
	}

	/**
	 * Registers a user
	 *
	 * @return string
	 */
	public function register()
	{
		// check if user already exists
		try
		{
			$user = $this->findByLogin($this->{$this->loginColumn});
		}
		catch (UserNotFoundException $e)
		{
			$user = null;
		}

		// see if the user already exists and is activated
		// if so, throw exception
		if ($user and $user->activated)
		{
			throw new UserExistsException;
		}
		// if the user does exist, but is not activated
		// just generate a new activation code and upate the user
		elseif ($user)
		{
			// generate an activation code
			$activationCode = $this->randomString();

			$user->activation_hash = $activationCode;
			$user->save();
		}
		// otherwise add the activation code and save the new user
		else
		{
			// generate an activation code
			$activationCode = $this->randomString();

			$this->activation_hash = $activationCode;
			$this->activated = 0;
			$this->save();
		}

		return $activationCode;
	}

}