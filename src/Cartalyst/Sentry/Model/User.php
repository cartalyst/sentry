<?php namespace Cartalyst\Sentry\Model;

use Illuminate\Database\Eloquent\Model as EloquentModel;


class User extends EloquentModel
{
	protected $table = 'users';

	protected $hidden = array('password');

	public $loginColumn = 'email';
}