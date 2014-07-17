<?php namespace Cartalyst\Sentry\Resources;


interface ResourceInterface {

    /**
     * Returns the resource ID.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Returns the resouce name.
     *
     * @return string
     */
    public function getName();

    /**
     * Return resource value
     * @return string
     */
    public function getValue();

    /**
     * return parent id
     * @return int
     */
    public function getParentId();

}
