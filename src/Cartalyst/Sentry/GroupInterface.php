<?php namespace Cartalyst\Sentry;

interface GroupInterface
{
	public function findById($id);

	public function findByName($login);
}