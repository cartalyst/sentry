<?php namespace Cartalyst\Sentry\Auth\Providers;


Interface AuthProviderInferface{

    /**
     * metoda autoryzacyjna
     * @param array $credentianls
     * @return mixed
     */
    public function authorize(array $credentianls);
}