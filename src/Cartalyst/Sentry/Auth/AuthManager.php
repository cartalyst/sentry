<?php
namespace Cartalyst\Sentry\Auth;

class AuthManager {

    /**
     * Authentication providers
     *
     * @var array
     */
    protected $providers = [];

    protected $default = null;


    /**
     * ustawia defaultowego providera
     * @param string $name
     * @throws Exception
     */
    public function setDefault($name){
        if(!$this->has(strtolower($name))){
            throw new Exception("$name nie jest zarejestrowany w managerze");
        }

        $this->default = $name;
    }

    /**
     * zwraca defaultowego providera
     * @return null
     */
    public function getDefault(){
        return $this->default;
    }

    /**
     * sprawdzamy, czy mamy wskazanego providera
     *
     * @param string $name
     * @return bool
     */
    public function has($name){
        return isset($this->providers[strtolower($name)]);
    }

    /**
     * rejestruje providera w managerze
     *
     * @return void
     */
    public function set($name, $provider) {
        $this->providers[strtolower($name)] = $provider;
    }

    /**
     * zwraca wskazanego providera
     *
     * @return mixed
     */
    public function get($name){
        if($this->has(strtolower($name))){
            return $this->providers[strtolower($name)];
        }

        throw new Exception("$name nie jest zarejestrowany w managerze");
    }

}