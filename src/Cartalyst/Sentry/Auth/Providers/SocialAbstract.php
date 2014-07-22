<?php namespace Cartalyst\Sentry\Auth\Providers;


abstract class SocialAbstract implements AuthProviderInferface {
    /**
     * OAuth Provider
     */
    private $_provider;

    /**
     * metoda do autoryzacji
     * @param array $credentials
     * @param bool $remember
     * @return mixed
     */
    public abstract function authorize(array $credentials, $remember=false);

    /**
     * rejestracja uzytkownika w systemie
     * @return mixed
     */
    public abstract function register();
}