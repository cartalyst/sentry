<?php
namespace Cartalyst\Sentry\Auth;
use Cartalyst\Sentry\Auth\Providers;

class AuthManager {

    /**
     * Authentication providers
     *
     * @var array
     */
    protected $providers = [];

    /**
     * providers instances
     *
     * @var array
     */
    protected $instances = [];

    protected $current = null;


    /**
     * ustawia defaultowego providera
     * @param string $name
     * @throws Exception
     */
    public function setCurrent($name){
        if(!$this->has(strtolower($name))){
            throw new Exception("$name nie jest zarejestrowany w managerze");
        }

        $this->current = $name;
    }

    /**
     * zwraca defaultowego providera
     * @return null
     */
    public function getCurrent(){
        return $this->get($this->current);
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

        if ($this->current === null){
            $this->setCurrent($name);
        }
    }

    /**
     * zwraca obiekt wybranego providera
     *
     * @return mixed
     */
    public function get($name){
        $name = strtolower($name);

        if($this->has($name)){
            if (!isset($this->instances[$name])){
                $fullclass = __NAMESPACE__ . '\\' . 'Providers\\'.$this->providers[strtolower($name)];
                $this->instances[$name] = new $fullclass;
            }
            return $this->instances[$name];
        }

        throw new Exception("$name nie jest zarejestrowany w managerze");
    }

}