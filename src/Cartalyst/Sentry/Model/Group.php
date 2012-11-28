<?php namespace Cartalyst\Sentry\Model;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Cartalyst\Sentry\GroupInterface;


class Group extends EloquentModel implements GroupInterface
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'groups';

	/**
	 * -----------------------------------------
	 * GroupInterface Methods
	 * -----------------------------------------
	 */

	/**
	 * Find group by id
	 *
	 * @param   integer  $id
	 * @return  Cartalyst\Sentry\GroupInterface or false
	 */
	public function findById($id)
	{
		$user = $this->where($this->key, '=', $id)->first();

		return ($user) ?: false;
	}

	/**
	 * Find group by name
	 *
	 * @param   string  $name
	 * @return  Cartalyst\Sentry\GroupInterface or false
	 */
	public function findByName($login)
	{
		$user = $this->where('name', '=', $login)->first();

		return ($user) ?: false;
	}
}