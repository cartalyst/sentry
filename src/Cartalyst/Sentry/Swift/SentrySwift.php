<?php namespace Cartalyst\Sentry\Swift;
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

use ApiBase;
use Cartalyst\Sentry\Users\UserInterface;
use SwiftIdentityExpressApi;

class SentrySwift implements SwiftInterface {

	protected $api;

	protected $email;

	protected $password;

	protected $apiKey;

	protected $appCode;

	protected $ipAddress;

	protected $method = 'swipe';

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected $model = 'Cartalyst\Sentry\Swift\EloquentSwift';

	/**
	 * Create a new Illuminate swift repository.
	 *
	 * @param  string  $email
	 * @param  string  $password
	 * @param  string  $apiKey
	 * @param  string  $appCode
	 * @param  string  $method
	 * @param  string  $model
	 */
	public function __construct($email, $password, $apiKey, $appCode, $ipAddress, $method = null,  $model = null)
	{
		$this->email = $email;
		$this->password = $password;
		$this->apiKey = $apiKey;
		$this->appCode = $appCode;
		$this->ipAddress = $ipAddress;

		if (isset($method))
		{
			$this->method = $method;
		}

		if (isset($model))
		{
			$this->model = $model;
		}
	}

	/**
	 * Destory the object instance
	 */
	public function __destruct()
	{
		$this->disconnect();
	}

	/**
	 * {@inheritDoc}
	 */
	public function response(UserInterface $user)
	{
		$api = $this->getApi();

		$response = $api->doSecondFactor($user->getUserLogin(), $this->appCode, $this->ipAddress);
		$code = ApiBase::dispatchUser($response);

		return array($response, $code);
	}

	/**
	 * {@inheritDoc}
	 */
	public function smsResponse(UserInterface $user, $code)
	{

	}

	/**
	 * {@inheritDoc}
	 */
	public function saveNumber(UserInterface $user, $number)
	{

	}

	protected function modelForUser(UserInterface $user)
	{
		$model = $this
			->createModel()
			->where('user_id', $user->getUserId())
			->where('method', $this->method)
			->first();

		if ( ! $model)
		{
			$model = $this
				->createModel()
				->fill(array(
					'method' => $this->method,
				));

			$model->user()->associate($user);

			$model->save();
		}

		return $model;
	}

	protected function getApi()
	{
		if ($this->api === null)
		{
			$this->api = $this->connect();
		}

		return $this->api;
	}

	protected function connect()
	{
		$api = $this->createApi();
		$api->startTransaction();
		$api->apiLogin($this->email, $this->password, $this->apiKey);

		return $api;
	}

	protected function createApi()
	{
		return new SwiftIdentityExpressApi('https://api.swiftidentity.com/rs/expressapi/1.0/xml/');
	}

	protected function disconnect()
	{
		if ($this->api !== null)
		{
			$this->api->endTransaction();
		}
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
