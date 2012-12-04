<?php namespace Cartalyst\Sentry\Provider;

use Cartalyst\Sentry\ProviderInterface;
use Cartalyst\Sentry\UserInterface;
use Cartalyst\Sentry\GroupInterface;
use Cartalyst\Sentry\Model\User;
use Cartalyst\Sentry\Model\Group;
use Cartalyst\Sentry\Model\Throttle;
use Cartalyst\Sentry\HashInterface;

class Eloquent implements ProviderInterface
{
	/**
	 * The user interface
	 *
	 * @var  Cartalyst\Sentry\UserInterface
	 */
	protected $userInterface;

	/**
	 * The group interface
	 *
	 * @var  Cartalyst\Sentry\GroupInterface
	 */
	protected $groupInterface;

	/**
	 * The throttle interface
	 *
	 * @var  Cartalyst\Sentry\ThrottleInterface
	 */
	protected $throttleInterface;

	/**
	 * Constructor
	 *
	 * @return  Cartalyst\Sentry\ProviderInterface
	 */
	public function __construct(HashInterface $hashInterface = null)
	{
		$this->userInterface = new User(array(), $hashInterface);
		$this->groupInterface = new Group();
		$this->throttleInterface = new Throttle();
	}

	/**
	 * Get user interface
	 *
	 * @return  Cartalyst\Sentry\UserInterface
	 */
	public function userInterface()
	{
		return $this->userInterface;
	}

	/**
	 * Get user interface
	 *
	 * @return  Cartalyst\Sentry\GroupInterface
	 */
	public function groupInterface()
	{
		return $this->groupInterface;
	}

	/**
	 * Get user interface
	 *
	 * @return  Cartalyst\Sentry\ThrottleInterface
	 */
	public function throttleInterface()
	{
		return $this->throttleInterface;
	}

	/**
	 * Registers a user with activation code
	 *
	 * @return  string
	 */
	public function registerUser(array $attributes)
	{
		$user = $this->userInterface->fill($attributes);
		return $user->register();
	}

	/**
	 * Creates a user
	 *
	 * @return  string
	 */
	public function createUser(array $attributes)
	{
		$user = $this->userInterface->fill($attributes);
		return $this->save($user);
	}

	/**
	 * Creates a Group
	 *
	 * @return bool
	 */
	public function createGroup(array $attributes)
	{
		$group = $this->groupInterface->fill($attributes);
		return $this->save($group);
	}

	public function saveUser(UserInterface $user)
	{
		return $this->save($user);
	}

	public function saveGroup(GroupInterface $group)
	{
		return $this->save($group);
	}

	/**
	 * Registers a user
	 *
	 * @return
	 */
	protected function save($object)
	{
		if ( ! $object instanceof UserInterface and ! $object instanceof GroupInterface)
		{
			throw new \Exception('invalid object');
		}

		return $object->save();
	}

	public function deleteUser(UserInterface $user)
	{
		return $this->delete($user);
	}

	public function deleteGroup(GroupInterface $group)
	{
		return $this->delete($group);
	}

	/**
	 * Registers a user
	 *
	 * @return
	 */
	protected function delete($object)
	{
		if ( ! $object instanceof UserInterface and ! $object instanceof GroupInterface)
		{
			throw new InvalidObjectException;
		}

		return $object->delete();
	}
}