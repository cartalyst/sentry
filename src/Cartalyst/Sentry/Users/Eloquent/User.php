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

use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentry\Users\UserInterface;

class User extends Model implements UserInterface {

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = array(
		'password',
		'reset_password_hash',
		'activation_hash',
	);

	/**
	 * The login attribute.
	 *
	 * @var string
	 */
	protected $loginAttribute = 'email';

	/**
	 * Allowed Permissions Values
	 * options:
	 *   -1 => deny
	 *    0 => delete
	 *    1 => add
	 */
	protected $allowedPermissionsValues = array(-1, 0, 1);

	/**
	 * Super user permissions, gives access to everything
	 *
	 * @var string
	 */
	protected $superUser = 'superuser';

	/**
	 * Hashing Interface
	 *
	 * @var Cartalyst\Sentry\HashInterface
	 */
	protected $hashInterface;

	/**
	 * Fields that should be hashed
	 *
	 * @var array
	 */
	protected $hashedFields = array(
		'password',
		'reset_password_hash',
		'activation_hash',
	);

	/**
	 * Returns the user's ID.
	 *
	 * @return  mixed
	 */
	public function getUserId()
	{
		return $this->getKey();
	}

	/**
	 * Returns the user's login.
	 *
	 * @return mixed
	 */
	public function getUserLogin()
	{
		return $this->{$this->loginAttribute};
	}

	/**
	 * Returns the user's password (hashed).
	 *
	 * @return string
	 */
	public function getUserPassword()
	{
		return $this->password;
	}

	/**
	 * Check if user is activated
	 *
	 * @param  UserInterface  $user
	 * @return bool
	 */
	public function isActivated()
	{
		return $this->activated;
	}

	/**
	 * Get mutator for the activated property.
	 *
	 * @param  mixed  $activated
	 * @return bool
	 */
	public function getActivated($activated)
	{
		return (bool) $activated;
	}

	/**
	 * Returns the login attribute name.
	 *
	 * @return string
	 */
	public function getLoginAttributeName()
	{
		return $this->loginAttribute;
	}

}