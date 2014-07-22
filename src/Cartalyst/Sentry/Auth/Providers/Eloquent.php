<?php namespace Cartalyst\Sentry\Auth\Providers;



class EloquentProvider implements AuthProviderInferface{
    /**
     * metoda autoryzacyjna
     * @param array $credentianls
     * @return User
     */
    public function authorize(array $credentials, $remember=false){
        $userProvider = \App::make('sentry')->getUserProvider();
        $throttleProvider = \App::make('sentry')->getThrottleProvider();

        // We'll default to the login name field, but fallback to a hard-coded
        // 'login' key in the array that was passed.
        $loginName = $userProvider->getEmptyUser()->getLoginName();
        $loginCredentialKey = (isset($credentials[$loginName])) ? $loginName : 'login';

        if (empty($credentials[$loginCredentialKey]))
        {
            throw new LoginRequiredException("The [$loginCredentialKey] attribute is required.");
        }

        if (empty($credentials['password']))
        {
            throw new PasswordRequiredException('The password attribute is required.');
        }

        // If the user did the fallback 'login' key for the login code which
        // did not match the actual login name, we'll adjust the array so the
        // actual login name is provided.
        if ($loginCredentialKey !== $loginName)
        {
            $credentials[$loginName] = $credentials[$loginCredentialKey];
            unset($credentials[$loginCredentialKey]);
        }

        // If throttling is enabled, we'll firstly check the throttle.
        // This will tell us if the user is banned before we even attempt
        // to authenticate them
        if ($throttlingEnabled = $throttleProvider->isEnabled())
        {
            if ( $throttle = $throttleProvider->findByUserLogin($credentials[$loginName], \App::make('sentry')->getIpAddress()) )
            {
                $throttle->check();
            }
        }

        try
        {
            $user = $userProvider->findByCredentials($credentials);
        }
        catch (UserNotFoundException $e)
        {
            if ($throttlingEnabled and isset($throttle))
            {
                $throttle->addLoginAttempt();
            }

            throw $e;
        }

        if ($throttlingEnabled and isset($throttle))
        {
            $throttle->clearLoginAttempts();
        }

        $user->clearResetPassword();

        return $user;
    }
}