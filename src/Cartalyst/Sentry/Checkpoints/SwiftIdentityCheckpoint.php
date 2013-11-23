<?php namespace Cartalyst\Sentry\Checkpoints;
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

use Cartalyst\Sentry\Swift\SwiftRepositoryInterface;
use Cartalyst\Sentry\Users\UserInterface;

class SwiftIdentityCheckpoint implements CheckpointInterface {

	protected $swift;

	public function __construct(SwiftRepositoryInterface $swift)
	{
		$this->swift = $swift;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle(UserInterface $user = null)
	{
		if ($user === null)
		{
			return;
		}

		$response = $this->swift->passes($user);

		switch ($response)
		{
			case NEED_REGISTER_SMS:
				dd('redirect the user to a sms registration page');
				break;

			case NEED_REGISTER_SWIPE:
				dd('redirect the user to a swipe registration process');
				break;

			case RC_SWIPE_TIMEOUT:
				dd('Invalidate the session');
				break;

			case RC_SWIPE_ACCEPTED:
				dd('The user has reply OK to the swipe');
				break;

			case RC_SWIPE_REJECTED:
				dd('Invalidate the session');
				break;

			case RC_SMS_DELIVERED:
				dd('Redirect the user to an SMS Answer form page');
				break;

			case RC_ERROR:
				dd('An error happened, redirect to an error page. You may have forgot to do the api-login');
				break;

			case RC_APP_DOES_NOT_EXIST:
				dd('The application doesn\'t exist, you have an error in your application code');
				break;
		}
	}

}
