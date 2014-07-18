<?php namespace Cartalyst\Sentry;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;


class AclFilter {

    /**
     * filtruje prawa dostepu do zasobu
     * @param $route
     * @param $request
     * @return bool
     */
    public function filter($route, $request)
    {
        $role = $this->getRole();

        return $role->hasAccess($route->getActionName());
    }

    /**
     * @return array
     */
    public function getRole(){
        $user = \App::make('sentry')->getUser();

        if ($user){
            return $user;

        }
        else{
            $groupProvider = \App::make('sentry')->getGroupProvider();
            $defaultRole= $groupProvider->findByCode('guest');

            return $defaultRole;
        }
    }

}