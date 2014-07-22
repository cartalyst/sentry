<?php namespace Cartalyst\Sentry\Auth\Providers;


Interface AuthProviderInferface{

    /**
     * metoda autoryzacyjna
     * @param array $credentianls
     * @param boolean $remember
     * @return mixed
     */
    public function authorize(array $credentials, $remember=false);
}