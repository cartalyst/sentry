<?php namespace Cartalyst\Sentry;
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

use Cartalyst\Sentry\Cookies\IlluminateCookie;
use Cartalyst\Sentry\Groups\Eloquent\Provider as GroupProvider;
use Cartalyst\Sentry\Hashing\BcryptHasher;
use Cartalyst\Sentry\Hashing\NativeHasher;
use Cartalyst\Sentry\Hashing\Sha256Hasher;
use Cartalyst\Sentry\Hashing\WhirlpoolHasher;
use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Sessions\IlluminateSession;
use Cartalyst\Sentry\Throttling\Eloquent\Provider as ThrottleProvider;
use Cartalyst\Sentry\Users\Eloquent\Provider as UserProvider;
use Illuminate\Support\ServiceProvider;
use Cartalyst\Sentry\Auth\AuthManager;

class SentryServiceProvider extends ServiceProvider {

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('netinteractive/sentry', 'cartalyst/sentry');
        $this->app['sentry.auth.manager']->set('eloquent', $this->app['sentry.auth.providers.eloquent']);
        include './routes.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAuth();
        $this->registerHasher();
        $this->registerUserProvider();
        $this->registerGroupProvider();
        $this->registerThrottleProvider();
        $this->registerSession();
        $this->registerCookie();
        $this->registerSentry();
    }

    protected function registerAuth(){

        $this->app['sentry.auth.manager'] = $this->app->share(function($app)
        {
            return new AuthManager();
        });

        $this->app->singleton('AuthManager', function($app)
        {
            return $app['sentry.auth.manager'];
        });


        /**
         * Dodajemy domyslnegogo auth providera do managera
         *
         * @return void
         */
        $this->app['sentry.auth.providers.eloquent'] = $this->app->share(function($app)
        {
            return 'EloquentProvider';
        });
    }

    /**
     * Register the hasher used by Sentry.
     *
     * @return void
     */
    protected function registerHasher()
    {
        $this->app['sentry.hasher'] = $this->app->share(function($app)
        {
            $hasher = $app['config']['cartalyst/sentry::hasher'];

            switch ($hasher)
            {
                case 'native':
                    return new NativeHasher;
                    break;

                case 'bcrypt':
                    return new BcryptHasher;
                    break;

                case 'sha256':
                    return new Sha256Hasher;
                    break;

                case 'whirlpool':
                    return new WhirlpoolHasher;
                    break;
            }

            throw new \InvalidArgumentException("Invalid hasher [$hasher] chosen for Sentry.");
        });
    }

    /**
     * Register the user provider used by Sentry.
     *
     * @return void
     */
    protected function registerUserProvider()
    {
        $this->app['sentry.user'] = $this->app->share(function($app)
        {
            $model = $app['config']['cartalyst/sentry::users.model'];

            // We will never be accessing a user in Sentry without accessing
            // the user provider first. So, we can lazily set up our user
            // model's login attribute here. If you are manually using the
            // attribute outside of Sentry, you will need to ensure you are
            // overriding at runtime.
            if (method_exists($model, 'setLoginAttributeName'))
            {
                $loginAttribute = $app['config']['cartalyst/sentry::users.login_attribute'];

                forward_static_call_array(
                    array($model, 'setLoginAttributeName'),
                    array($loginAttribute)
                );
            }

            // Define the Group model to use for relationships.
            if (method_exists($model, 'setGroupModel'))
            {
                $groupModel = $app['config']['cartalyst/sentry::groups.model'];

                forward_static_call_array(
                    array($model, 'setGroupModel'),
                    array($groupModel)
                );
            }

            // Define the user group pivot table name to use for relationships.
            if (method_exists($model, 'setUserGroupsPivot'))
            {
                $pivotTable = $app['config']['cartalyst/sentry::user_groups_pivot_table'];

                forward_static_call_array(
                    array($model, 'setUserGroupsPivot'),
                    array($pivotTable)
                );
            }

            return new UserProvider($app['sentry.hasher'], $model);
        });
    }

    /**
     * Register the group provider used by Sentry.
     *
     * @return void
     */
    protected function registerGroupProvider()
    {
        $this->app['sentry.group'] = $this->app->share(function($app)
        {
            $model = $app['config']['cartalyst/sentry::groups.model'];

            // Define the User model to use for relationships.
            if (method_exists($model, 'setUserModel'))
            {
                $userModel = $app['config']['cartalyst/sentry::users.model'];

                forward_static_call_array(
                    array($model, 'setUserModel'),
                    array($userModel)
                );
            }

            // Define the user group pivot table name to use for relationships.
            if (method_exists($model, 'setUserGroupsPivot'))
            {
                $pivotTable = $app['config']['cartalyst/sentry::user_groups_pivot_table'];

                forward_static_call_array(
                    array($model, 'setUserGroupsPivot'),
                    array($pivotTable)
                );
            }

            return new GroupProvider($model);
        });
    }

    /**
     * Register the throttle provider used by Sentry.
     *
     * @return void
     */
    protected function registerThrottleProvider()
    {
        $this->app['sentry.throttle'] = $this->app->share(function($app)
        {
            $model = $app['config']['cartalyst/sentry::throttling.model'];

            $throttleProvider = new ThrottleProvider($app['sentry.user'], $model);

            if ($app['config']['cartalyst/sentry::throttling.enabled'] === false)
            {
                $throttleProvider->disable();
            }

            if (method_exists($model, 'setAttemptLimit'))
            {
                $attemptLimit = $app['config']['cartalyst/sentry::throttling.attempt_limit'];

                forward_static_call_array(
                    array($model, 'setAttemptLimit'),
                    array($attemptLimit)
                );
            }
            if (method_exists($model, 'setSuspensionTime'))
            {
                $suspensionTime = $app['config']['cartalyst/sentry::throttling.suspension_time'];

                forward_static_call_array(
                    array($model, 'setSuspensionTime'),
                    array($suspensionTime)
                );
            }

            // Define the User model to use for relationships.
            if (method_exists($model, 'setUserModel'))
            {
                $userModel = $app['config']['cartalyst/sentry::users.model'];

                forward_static_call_array(
                    array($model, 'setUserModel'),
                    array($userModel)
                );
            }

            return $throttleProvider;
        });
    }

    /**
     * Register the session driver used by Sentry.
     *
     * @return void
     */
    protected function registerSession()
    {
        $this->app['sentry.session'] = $this->app->share(function($app)
        {
            $key = $app['config']['cartalyst/sentry::cookie.key'];

            return new IlluminateSession($app['session.store'], $key);
        });
    }

    /**
     * Register the cookie driver used by Sentry.
     *
     * @return void
     */
    protected function registerCookie()
    {
        $this->app['sentry.cookie'] = $this->app->share(function($app)
        {
            $key = $app['config']['cartalyst/sentry::cookie.key'];

            /**
             * We'll default to using the 'request' strategy, but switch to
             * 'jar' if the Laravel version in use is 4.0.*
             */

            $strategy = 'request';

            if (preg_match('/^4\.0\.\d*$/D', $app::VERSION))
            {
                $strategy = 'jar';
            }

            return new IlluminateCookie($app['request'], $app['cookie'], $key, $strategy);
        });
    }

    /**
     * Takes all the components of Sentry and glues them
     * together to create Sentry.
     *
     * @return void
     */
    protected function registerSentry()
    {
        $this->app['sentry'] = $this->app->share(function($app)
        {
            return new Sentry(
                $app['sentry.user'],
                $app['sentry.group'],
                $app['sentry.throttle'],
                $app['sentry.session'],
                $app['sentry.cookie'],
                $app['request']->getClientIp()
            );
        });
    }

}
