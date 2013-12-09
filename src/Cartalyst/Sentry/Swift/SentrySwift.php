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
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use ApiBase;
use Cartalyst\Sentry\Users\UserInterface;
use Closure;
use SwiftIdentityExpressApi;

class SentrySwift implements SwiftInterface {

	/**
	 * The shared API instance.
	 *
	 * @var \SwiftIdentityExpressApi
	 */
	protected $api;

	/**
	 * The email address used to authenticate with the API.
	 *
	 * @var string
	 */
	protected $email;

	/**
	 * The password used to authenticate with the API.
	 *
	 * @var string
	 */
	protected $password;

	/**
	 * The key used to authenticate with the API.
	 *
	 * @var string
	 */
	protected $apiKey;

	/**
	 * The app code used to authenticate with the API.
	 *
	 * @var string
	 */
	protected $appCode;

	/**
	 * The IP address of the user authenticating.
	 *
	 * @var string
	 */
	protected $ipAddress;

	/**
	 * The Swift API method, "swipe" or "sms".
	 *
	 * @var string
	 */
	protected $method = 'swipe';

	/**
	 * Flag for whether the object is in an SMS answering state.
	 *
	 * @var bool
	 */
	protected $answering = false;

	/**
	 * Model name.
	 *
	 * @var string
	 */
	protected $model = 'Cartalyst\Sentry\Swift\EloquentSwift';

	/**
	 * Create a new Swift Identity.
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
	 * Destroy the object instance.
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
	public function saveNumber(UserInterface $user, $number)
	{
		$api = $this->getApi();

		$response = $api->setUserSmsNumber($user->getUserLogin(), $this->appCode, $number);

		return ($response->status == 1);
	}

	/**
	 * {@inheritDoc}
	 */
	public function checkAnswer(UserInterface $user, $answer, Closure $callback = null)
	{
		$this->answering = true;

		$api = $this->getApi();

		$response = $api->answerSMS($user->getUserLogin(), $this->appCode, $answer);

		if ($response->getReturnCode() == RC_SMS_ANSWER_REJECTED)
		{
			return false;
		}

		$response = isset($callback) ? $callback($user) : true;
		$this->answering = false;
		return $response;
	}

	/**
	 * Flag for whether the object is in an SMS answering state.
	 *
	 * @return bool
	 */
	public function isAnswering()
	{
		return $this->answering;
	}

	/**
	 * Lazily get an API instance associated with the object.
	 *
	 * @return \SwiftIdentityExpressApi
	 */
	public function getApi()
	{
		if ($this->api === null)
		{
			$this->api = $this->connect();
		}

		return $this->api;
	}

	/**
	 * Connect to the Swift Identity API.
	 *
	 * @return \SwiftIdentityExpressApi
	 */
	protected function connect()
	{
		$api = $this->createApi();
		$api->startTransaction();
		$api->apiLogin($this->email, $this->password, $this->apiKey);

		return $api;
	}

	/**
	 * Create a new Swift Identity API instance.
	 *
	 * @return \SwiftIdentityExpressApi
	 */
	protected function createApi()
	{
		return new SwiftIdentityExpressApi('https://api.swiftidentity.com/rs/expressapi/1.0/xml/');
	}

	/**
	 * Disconnects from the Swift Identity API.
	 *
	 * @return void
	 */
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
