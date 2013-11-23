<?php namespace Cartalyst\Sentry\Activations;
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

use Carbon\Carbon;
use Cartalyst\Sentry\Users\UserInterface;

/**
 * @todo Switch over to eager loading where possible, under the assumption that the Eloquent user model will include the required relationship.
 */
class IlluminateActivationRepository implements ActivationRepositoryInterface {

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected $model;

	/**
	 * Time, in minutes, in which activation codes expire.
	 *
	 * @var int
	 */
	protected $expires = 4320;

	/**
	 * Create a new Illuminate activation repository.
	 *
	 * @param  string  $model
	 * @param  int  $expires
	 */
	public function __construct($model, $expires = null)
	{
		$this->model = $model;

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
		$activation = $this->creatModel();

		$code = $this->generateActivationCode();

		$model->fill(array(
			'user_id' => $user->getUserId(),
			'code' => $code,
			'completed' => false,
		));

		$model->save();

		return $code;
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists(UserInterface $user)
	{
		$activation = $this
			->createModel()
			->where('user_id', $user->getUserId())
			->where('completed', true)
			->first();

		return ($activation !== null);
	}

	/**
	 * {@inheritDoc}
	 */
	public function complete(UserInterface $user, $code)
	{
		$activation = $this
			->createModel()
			->where('user_id', $user->getUserId())
			->where('code', $code)
			->first();

		if ($activation === null)
		{
			return false;
		}

		$activation->fill(array(
			'completed' => true,
			'completed_at' => Carbon::now(),
		));

		$activation->save();

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
	 * Return a random string for an activation code.
	 *
	 * @return string
	 */
	protected function generateActivationCode()
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

}
