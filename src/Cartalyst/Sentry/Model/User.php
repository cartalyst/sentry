<?php namespace Cartalyst\Sentry\Model;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Cartalyst\Sentry\UserInterface;
use Cartalyst\Sentry\UserGroupInterface;
use Cartalyst\Sentry\GroupInterface;


class User extends EloquentModel implements UserInterface, UserGroupInterface
{
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
	protected $hidden = array('password');

	/**
	 * The login column
	 *
	 * @var string
	 */
	protected $loginColumn = 'email';

	/**
	 * -----------------------------------------
	 * UserInterface Methods
	 * -----------------------------------------
	 */

	/**
	 * Get user login column
	 *
	 * @return  string
	 */
	public function getLoginColumn()
	{
		return $this->loginColumn;
	}

	/**
	 * Get user login column
	 *
	 * @param   integer  $id
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findById($id)
	{
		return $this->find($id);
	}

	/**
	 * Get user by login value
	 *
	 * @param   string  $login
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findByLogin($login)
	{
		$user = $this->where($this->loginColumn, '=', $login)->first();

		return ($user) ?: false;
	}

	/**
	 * Get user by credentials
	 *
	 * @param   string  $login
	 * @param   string  $password
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findByCredentials($login, $password)
	{
		$user = $this->findByLogin($login);

		if ($user and $password === $user->password)
		{
			return $user;
		}

		return false;
	}

	/**
	 * -----------------------------------------
	 * UserGroupInterface Methods
	 * -----------------------------------------
	 */

	/**
	 * Get user's groups
	 *
	 * @return Cartalyst\Sentry\GroupInterface
	 */
	public function getGroups()
	{
		return $this->groups()->where('user_id', '=', 1)->get();
	}

	/**
	 * Add user to group
	 *
	 * @param   integer|Cartalyst\Sentry\GroupInterface  $group
	 * @return  bool
	 */
	public function addGroup($group)
	{
		if ( $group instanceof GroupInterface or is_int($group))
		{
			return $this->groups()->attach($group);
		}

		return false;
	}

	/**
	 * Add user to multiple groups
	 *
	 * @param   array  $groups integer|Cartalyst\Sentry\GroupInterface
	 */
	public function addGroups(array $groups)
	{
		foreach ($groups as $group)
		{
			$this->addGroup($group);
		}
	}

	/**
	 * Remove user from group
	 *
	 * @param   integer|Cartalyst\Sentry\GroupInterface  $group
	 * @return  bool
	 */
	public function removeGroup($group)
	{
		if ( $group instanceof GroupInterface or is_int($group))
		{
			return $this->groups()->detach($group);
		}

		return false;
	}

	/**
	 * Remove user from multiple groups
	 *
	 * @param   array  $groups integer|Cartalyst\Sentry\GroupInterface
	 */
	public function removeGroups(array $groups)
	{
		foreach ($groups as $group)
		{
			$this->removeGroup($group);
		}
	}

	/**
	 * See if user is in a group
	 *
	 * @param   integer  $group
	 * @return  bool
	 */
	public function inGroup($group)
	{
		foreach ($this->getGroups() as $_group)
		{
			if ($group == $_group->id)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * relate users to groups
	 */
	protected function groups()
	{
		return $this->belongsToMany(__NAMESPACE__.'\\Group', 'user_group', 'user_id', 'group_id');
	}

}