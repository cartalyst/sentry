<?php namespace Cartalyst\Sentry;

interface UserInterface
{
	/**
	 * Get user login column
	 *
	 * @return  string
	 */
	public function getLoginColumn();

	/**
	 * Get user login column
	 *
	 * @param   integer  $id
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findById($id);

	/**
	 * Get user by login value
	 *
	 * @param   string  $login
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findByLogin($login);

	/**
	 * Get user by credentials
	 *
	 * @param   string  $login
	 * @param   string  $password
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function findByCredentials($login, $password);

}