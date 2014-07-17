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
        return true;
    }

    /**
     * @return array
     */
    public function getPermissions(){
        $user = \App::make('sentry')->getUser();

        if ($user){
            return $user->getPermissions();

        }
        else{
            $groupProvider = \App::make('sentry')->getGroupProvider();
            $defaultGroup = $groupProvider->findByName('guest');

            return $defaultGroup->getPermissions(); exit;
        }
    }

}