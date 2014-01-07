<?php namespace Cartalyst\Sentry\Reminders;
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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Carbon\Carbon;
use Cartalyst\Sentry\Users\UserInterface;
use Cartalyst\Sentry\Users\UserRepositoryInterface;

/**
 * @todo Switch over to eager loading where possible, under the assumption that the Eloquent user model will include the required relationship.
 */
class IlluminateReminderRepository implements ReminderRepositoryInterface {

	/**
	 * User repository.
	 *
	 * @var \Cartalyst\Sentry\Users\UserRepositoryInterface
	 */
	protected $users;

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected $model = 'Cartalyst\Sentry\Reminders\EloquentReminder';

	/**
	 * Time, in seconds, in which reminder codes expire.
	 *
	 * @var int
	 */
	protected $expires = 259200;

	/**
	 * Create a new Illuminate reminder repository.
	 *
	 * @param  \Cartalyst\Sentry\Users\UserRepositoryInterface
	 * @param  string  $model
	 * @param  int  $expires
	 * @return void
	 */
	public function __construct(UserRepositoryInterface $users, $model = null, $expires = null)
	{
		$this->users = $users;

		if (isset($model))
		{
			$this->model = $model;
		}

		if (isset($expires))
		{
			$this->expires = $expires;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(UserInterface $user)
	{
		$reminder = $this->createModel();

		$code = $this->generateReminderCode();

		$reminder->fill(array(
			'code' => $code,
			'completed' => false,
		));

		$reminder->user_id = $user->getUserId();

		$reminder->save();

		return $code;
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists(UserInterface $user)
	{
		$reminder = $this
			->createModel()
			->where('user_id', $user->getUserId())
			->where('completed', true)
			->first();

		return ($reminder !== null);
	}

	/**
	 * {@inheritDoc}
	 */
	public function complete(UserInterface $user, $code, $password)
	{
		$reminder = $this
			->createModel()
			->where('user_id', $user->getUserId())
			->where('code', $code)
			->first();

		if ($reminder === null)
		{
			return false;
		}

		$credentials = compact('password');

		$valid = $this->users->validForUpdate($user, $credentials);

		if ($valid === false)
		{
			return false;
		}

		$this->users->update($user, $credentials);

		$reminder->fill(array(
			'completed' => true,
			'completed_at' => Carbon::now(),
		));

		$reminder->save();

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleteExpired()
	{
		$expired = Carbon::now()->subMinutes($this->expires);

		return $this
			->createModel()
			->newQuery()
			->where('completed', false)
			->where('created_at', '<', $expires)
			->delete();
	}

	/**
	 * Return a random string for an reminder code.
	 *
	 * @return string
	 */
	protected function generateReminderCode()
	{
		return str_random(32);
	}

	/**
	 * Create a new instance of the model.
	 *
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function createModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

	/**
	 * Runtime override of the model.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public function setModel($model)
	{
		$this->model = $model;
	}

}
