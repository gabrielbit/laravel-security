<?php namespace Digbang\Security;

use Cartalyst\Sentinel\Activations\ActivationRepositoryInterface;
use Cartalyst\Sentinel\Checkpoints\CheckpointInterface;
use Cartalyst\Sentinel\Persistences\PersistenceRepositoryInterface;
use Cartalyst\Sentinel\Reminders\ReminderRepositoryInterface;
use Cartalyst\Sentinel\Roles\RoleRepositoryInterface;
use Cartalyst\Sentinel\Sentinel;
use Cartalyst\Sentinel\Users\UserRepositoryInterface;
use Closure;
use Digbang\Security\Roles\Role;
use Digbang\Security\Users\User;
use Digbang\Security\Contracts\SecurityApi;
use Illuminate\Support\Collection;

/**
 * Class Security
 *
 * @package Digbang\Security
 *
 * @method User findById(int $id)
 * @method User findByCredentials(array $credentials)
 * @method User findByPersistenceCode(string $code)
 * @method User|bool recordLogin(User $user)
 * @method User|bool recordLogout(User $user)
 * @method bool validateCredentials(User $user, array $credentials)
 * @method bool validForCreation(array $credentials)
 * @method bool validForUpdate($user, array $credentials)
 * @method User create(array $credentials, Closure $callback = null)
 * @method User update($user, array $credentials)
 * @method User findUserById(int $id)
 * @method User findUserByCredentials(array $credentials)
 * @method User findUserByPersistenceCode(string $code)
 * @method Role findRoleById(int $id)
 * @method Role findRoleBySlug(string $slug)
 * @method Role findRoleByName(string $name)
 */
final class Security implements SecurityApi
{
	/**
	 * @type Sentinel
	 */
	private $sentinel;

	/**
	 * @type bool
	 */
	private $hasRoles;

	/**
	 * @type bool
	 */
	private $hasPermissions;

	/**
	 * Security constructor.
	 *
	 * @param Sentinel $sentinel
	 * @param bool     $hasRoles
	 * @param bool     $hasPermissions
	 */
	public function __construct(Sentinel $sentinel, $hasRoles = true, $hasPermissions = true)
	{
		$this->sentinel = $sentinel;
		$this->hasRoles       = (bool) $hasRoles;
		$this->hasPermissions = (bool) $hasPermissions;
	}

	/**
	 * Registers a user. You may provide a callback to occur before the user
	 * is saved, or provide a true boolean as a shortcut to activation.
	 *
	 * @param  array         $credentials
	 * @param  \Closure|bool $callback
	 *
	 * @return User|bool
	 * @throws \InvalidArgumentException
	 */
	public function register(array $credentials, $callback = null)
	{
		return $this->sentinel->register($credentials, $callback);
	}

	/**
	 * Registers and activates the user.
	 *
	 * @param  array $credentials
	 *
	 * @return User|bool
	 */
	public function registerAndActivate(array $credentials)
	{
		return $this->sentinel->registerAndActivate($credentials);
	}

	/**
	 * Activates the given user.
	 *
	 * @param  mixed $user
	 *
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function activate($user)
	{
		return $this->sentinel->activate($user);
	}

	/**
	 * Checks to see if a user is logged in.
	 *
	 * @return User|bool
	 */
	public function check()
	{
		return $this->sentinel->check();
	}

	/**
	 * Checks to see if a user is logged in, bypassing checkpoints
	 *
	 * @return User|bool
	 */
	public function forceCheck()
	{
		return $this->sentinel->forceCheck();
	}

	/**
	 * Checks if we are currently a guest.
	 *
	 * @return User|bool
	 */
	public function guest()
	{
		return $this->sentinel->guest();
	}

	/**
	 * Authenticates a user, with "remember" flag.
	 *
	 * @param  User|array $credentials
	 * @param  bool       $remember
	 * @param  bool       $login
	 *
	 * @return User|bool
	 */
	public function authenticate($credentials, $remember = false, $login = true)
	{
		return $this->sentinel->authenticate($credentials, $remember, $login);
	}

	/**
	 * Authenticates a user, with the "remember" flag.
	 *
	 * @param  User|array $credentials
	 *
	 * @return User|bool
	 */
	public function authenticateAndRemember($credentials)
	{
		return $this->sentinel->authenticateAndRemember($credentials);
	}

	/**
	 * Forces an authentication to bypass checkpoints.
	 *
	 * @param  User|array $credentials
	 * @param  bool       $remember
	 *
	 * @return User|bool
	 */
	public function forceAuthenticate($credentials, $remember = false)
	{
		return $this->sentinel->forceAuthenticate($credentials, $remember);
	}

	/**
	 * Forces an authentication to bypass checkpoints, with the "remember" flag.
	 *
	 * @param  User|array $credentials
	 *
	 * @return User|bool
	 */
	public function forceAuthenticateAndRemember($credentials)
	{
		return $this->sentinel->forceAuthenticateAndRemember($credentials);
	}

	/**
	 * Attempt a stateless authentication.
	 *
	 * @param  User|array $credentials
	 *
	 * @return User|bool
	 */
	public function stateless($credentials)
	{
		return $this->sentinel->stateless($credentials);
	}

	/**
	 * Attempt to authenticate using HTTP Basic Auth.
	 *
	 * @return mixed
	 */
	public function basic()
	{
		return $this->sentinel->basic();
	}

	/**
	 * Returns the request credentials.
	 *
	 * @return array
	 */
	public function getRequestCredentials()
	{
		return $this->sentinel->getRequestCredentials();
	}

	/**
	 * Sets the closure which resolves the request credentials.
	 *
	 * @param  \Closure $requestCredentials
	 *
	 * @return void
	 */
	public function setRequestCredentials(Closure $requestCredentials)
	{
		$this->sentinel->setRequestCredentials($requestCredentials);
	}

	/**
	 * Sends a response when HTTP basic authentication fails.
	 *
	 * @return mixed
	 * @throws \RuntimeException
	 */
	public function getBasicResponse()
	{
		return $this->sentinel->getBasicResponse();
	}

	/**
	 * Sets the callback which creates a basic response.
	 *
	 * @param Closure $basicResponse
	 *
	 * @return void
	 */
	public function creatingBasicResponse(Closure $basicResponse)
	{
		$this->sentinel->creatingBasicResponse($basicResponse);
	}

	/**
	 * Persists a login for the given user.
	 *
	 * @param  User $user
	 * @param  bool $remember
	 *
	 * @return User|bool
	 */
	public function login(User $user, $remember = false)
	{
		return $this->sentinel->login($user, $remember);
	}

	/**
	 * Persists a login for the given user, with the "remember" flag.
	 *
	 * @param  User $user
	 *
	 * @return User|bool
	 */
	public function loginAndRemember(User $user)
	{
		return $this->sentinel->loginAndRemember($user);
	}

	/**
	 * Logs the current user out.
	 *
	 * @param  User $user
	 * @param  bool $everywhere
	 *
	 * @return bool
	 */
	public function logout(User $user = null, $everywhere = false)
	{
		return $this->sentinel->logout($user, $everywhere);
	}

	/**
	 * Pass a closure to Sentinel to bypass checkpoints.
	 *
	 * @param  Closure $callback
	 * @param  array   $checkpoints
	 *
	 * @return mixed
	 */
	public function bypassCheckpoints(Closure $callback, $checkpoints = [])
	{
		return $this->sentinel->bypassCheckpoints($callback, $checkpoints);
	}

	/**
	 * Checks if checkpoints are enabled.
	 *
	 * @return bool
	 */
	public function checkpointsStatus()
	{
		return $this->sentinel->checkpointsStatus();
	}

	/**
	 * Enables checkpoints.
	 *
	 * @return void
	 */
	public function enableCheckpoints()
	{
		$this->sentinel->enableCheckpoints();
	}

	/**
	 * Disables checkpoints.
	 *
	 * @return void
	 */
	public function disableCheckpoints()
	{
		$this->sentinel->disableCheckpoints();
	}

	/**
	 * Add a new checkpoint to Sentinel.
	 *
	 * @param  string              $key
	 * @param  CheckpointInterface $checkpoint
	 *
	 * @return void
	 */
	public function addCheckpoint($key, CheckpointInterface $checkpoint)
	{
		$this->sentinel->addCheckpoint($key, $checkpoint);
	}

	/**
	 * Removes a checkpoint.
	 *
	 * @param  string $key
	 *
	 * @return void
	 */
	public function removeCheckpoint($key)
	{
		$this->sentinel->removeCheckpoint($key);
	}

	/**
	 * Returns the currently logged in user, lazily checking for it.
	 *
	 * @param  bool $check
	 *
	 * @return User
	 */
	public function getUser($check = true)
	{
		return $this->sentinel->getUser($check);
	}

	/**
	 * Sets the user associated with Sentinel (does not log in).
	 *
	 * @param User $user
	 *
	 * @return void
	 */
	public function setUser(User $user)
	{
		$this->sentinel->setUser($user);
	}

	/**
	 * Returns the user repository.
	 *
	 * @return UserRepositoryInterface
	 */
	public function users()
	{
		return $this->sentinel->getUserRepository();
	}

	/**
	 * Returns the role repository.
	 *
	 * @return RoleRepositoryInterface
	 */
	public function roles()
	{
		return $this->sentinel->getRoleRepository();
	}

	/**
	 * Returns the persistences repository.
	 *
	 * @return PersistenceRepositoryInterface
	 */
	public function persistences()
	{
		return $this->sentinel->getPersistenceRepository();
	}

	/**
	 * Returns the reminders repository.
	 *
	 * @return ReminderRepositoryInterface
	 */
	public function reminders()
	{
		return $this->sentinel->getReminderRepository();
	}

	/**
	 * Returns the activations repository.
	 *
	 * @return ActivationRepositoryInterface
	 */
	public function activations()
	{
		return $this->sentinel->getActivationRepository();
	}

	/**
     * Dynamically pass missing methods to Sentinel.
     *
     * @param  string $method
     * @param  array  $parameters
     * @return mixed
     * @throws \BadMethodCallException
     */
	public function __call($method, $parameters)
	{
		return call_user_func_array([$this->sentinel, $method], $parameters);
	}

	/**
	 * @param array $permissions
	 * @param string ...$permissions
	 * @return bool
	 */
	public function hasAccess($permissions)
	{
		if (! $this->hasPermissions)
		{
			return true;
		}

		return call_user_func_array([$this->sentinel, 'hasAccess'], func_get_args());
	}

	/**
	 * @param array $permissions
	 * @param string ...$permissions
	 * @return bool
	 */
	public function hasAnyAccess($permissions)
	{
		if (! $this->hasPermissions)
		{
			return true;
		}

		return call_user_func_array([$this->sentinel, 'hasAnyAccess'], func_get_args());
	}

	/**
	 * @return Collection
	 */
	public function getRoles()
	{
		if (! $this->hasRoles)
		{
			return new Collection;
		}

		return call_user_func_array([$this->sentinel, 'getRoles'], func_get_args());
	}

	/**
	 * @param Role $role
	 * @return bool
	 */
	public function inRole(Role $role)
	{
		if (! $this->hasRoles)
		{
			return false;
		}

		return call_user_func_array([$this->sentinel, 'inRole'], func_get_args());
	}
}