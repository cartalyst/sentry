<?php namespace Cartalyst\Sentry\Resources;


interface ProviderInterface {

    /**
     * Find the resource by ID.
     *
     * @param  int  $id
     * @return \Cartalyst\Sentry\Resources\ResourceInterface $resource
     */
    public function findById($id);

    /**
     * Find the resource by name.
     *
     * @param  string  $name
     * @return \Cartalyst\Sentry\Resources\ResourceInterface $resource
     */
    public function findByName($name);

    /**
     * Find resource by value
     * @param string $value
     * @return mixed
     */
    public function findByValue($value);

    /**
     * find by parent id
     * @param int $id
     * @return mixed
     */
    public function findByParent($id);

    /**
     * Returns resource groups.
     *
     * @return array  $groups
     */
    public function findAll();

    /**
     * Creates a resource.
     *
     * @param  array  $attributes
     * @return \Cartalyst\Sentry\Resources\ResourceInterface
     */
    public function create(array $attributes);

}
