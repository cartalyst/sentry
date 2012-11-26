<?php namespace Cartalyst\Sentry\Model;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Cartalyst\Sentry\GroupInterface;


class Group extends EloquentModel implements GroupInterface
{
	protected $table = 'groups';

	/**
	 * -----------------------------------------
	 * GroupInterface Methods
	 * -----------------------------------------
	 */
	public function findById($id)
	{
		$user = $this->where($this->key, '=', $id)->first();

		return ($user) ?: false;
	}

	public function findByName($login)
	{
		$user = $this->where('name', '=', $login)->first();

		return ($user) ?: false;
	}
}