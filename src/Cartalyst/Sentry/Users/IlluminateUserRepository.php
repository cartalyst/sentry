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
	 * Create a new Illuminate user repository.
	 *
	 * @param  \Cartalyst\Sentry\Hashing\HasherInterface
	 */
	public function __construct(HasherInterface $hasher)
	{
		$this->hasher = $hasher;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findByCredentials(array $credentials)
	{
		$instance = new EloquentUser;
		$loginNames = $instance->getLoginNames();
		$query = $instance->newQuery();

		$passedLoginNames = array_intersect_key(array_flip($credentials), $loginNames);

		if (count($passedLoginNames) > 0)
		{
			$query->whereNested(function($query) use ($passedLoginNames, $credentials)
			{
				foreach ($passedLoginNames as $key)
				{
					$query->orWhere($key, $credentials[$key]);
				}
			});
		}
		elseif (array_key_exists('login', $credentials))
		{
			$value = $credentials['login'];
			$credentials[reset($passedLoginNames)] = $value;
		}
		else
		{
			throw new \InvalidArgumentException('Missing [login] creential.');
		}

		if ( ! array_key_exists('password', $credentials))
		{
			throw new \InvalidArgumentException('Missing [password] credential.');
		}

		foreach ($credentials as $key => $value)
		{
			if (array_key_exists($key, $passedLoginNames)) continue;

			$query->where($key, $value);
		}

		$user = $query->first();

		if ($user and $this->validateCredentials($user, $credentials['password']))
		{
			return $user;
		}
	}

	protected function validateCredentials(EloquentUser $user, $password)
	{
		return $this->hasher->check($password, $user->password);
	}

}
