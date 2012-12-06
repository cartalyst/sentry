<?php namespace Cartalyst\Sentry;

use RuntimeException;

class GroupNotFoundException extends RuntimeException {}
class GroupExistsException extends RuntimeException {}

interface GroupInterface
{
	/**
	 * Find group by id
	 *
	 * @param   integer  $id
	 * @return  Cartalyst\Sentry\GroupInterface or false
	 */
	public function findById($id);

	/**
	 * Find group by name
	 *
	 * @param   string  $name
	 * @return  Cartalyst\Sentry\GroupInterface or false
	 */
	public function findByName($login);
}