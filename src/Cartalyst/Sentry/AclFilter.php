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
    public function filter($route)
    {
        $roles = $this->getRoles();

        foreach ($roles AS $role){
            if ($role->hasAccess($route->getActionName())){
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getRoles(){
        $user = \App::make('sentry')->getUser();

        if ($user){
            $userRoles = $user->getGroups();
            if ($userRoles){
                return $userRoles;
            }
        }

        $groupProvider = \App::make('sentry')->getGroupProvider();
        $defaultRole= $groupProvider->findByCode('guest');

        return array($defaultRole);
    }

}