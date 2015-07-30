<?php namespace Digbang\Security\Configurations;

use Cartalyst\Sentinel\Activations\ActivationRepositoryInterface;
use Cartalyst\Sentinel\Checkpoints\ActivationCheckpoint;
use Cartalyst\Sentinel\Checkpoints\CheckpointInterface;
use Cartalyst\Sentinel\Checkpoints\ThrottleCheckpoint;
use Cartalyst\Sentinel\Permissions\PermissionsInterface;
use Cartalyst\Sentinel\Permissions\StandardPermissions;
use Cartalyst\Sentinel\Persistences\PersistenceRepositoryInterface;
use Cartalyst\Sentinel\Reminders\ReminderRepositoryInterface;
use Cartalyst\Sentinel\Roles\RoleRepositoryInterface;
use Cartalyst\Sentinel\Throttling\ThrottleRepositoryInterface;
use Cartalyst\Sentinel\Users\UserRepositoryInterface;
use Digbang\Doctrine\Metadata\EntityMapping;
use Digbang\Security\Mappings;
use Digbang\Security\Permissions\InsecurePermissionRepository;
use Digbang\Security\Permissions\PermissionRepository;

/**
 * Class SecurityContextConfiguration
 *
 * @package Digbang\Security\Configurations
 *
 * @method $this setUserMapping(EntityMapping $entityMapping)
 * @method $this setActivationMapping(EntityMapping $entityMapping)
 * @method $this setPermissionCollectionMapping(EntityMapping $entityMapping)
 * @method $this setPersistenceMapping(EntityMapping $entityMapping)
 * @method $this setReminderMapping(EntityMapping $entityMapping)
 * @method $this setRoleMapping(EntityMapping $entityMapping)
 * @method $this setThrottleMapping(EntityMapping $entityMapping)
 * @method $this setGlobalThrottleMapping(EntityMapping $entityMapping)
 * @method $this setIpThrottleMapping(EntityMapping $entityMapping)
 * @method $this setUserThrottleMapping(EntityMapping $entityMapping)
 * @method EntityMapping getUserMapping()
 * @method EntityMapping getActivationMapping()
 * @method EntityMapping getPermissionCollectionMapping()
 * @method EntityMapping getPersistenceMapping()
 * @method EntityMapping getReminderMapping()
 * @method EntityMapping getRoleMapping()
 * @method EntityMapping getThrottleMapping()
 * @method EntityMapping getGlobalThrottleMapping()
 * @method EntityMapping getIpThrottleMapping()
 * @method EntityMapping getUserThrottleMapping()
 * @method $this enableRoles()
 * @method $this enablePermissions()
 * @method $this disableRoles()
 * @method $this disablePermissions()
 * @method bool isRolesEnabled()
 * @method bool isPermissionsEnabled()
 * @method $this setGlobalThrottleInterval($interval)
 * @method $this setGlobalThrottleThresholds($thresholds)
 * @method $this setIpThrottleInterval($interval)
 * @method $this setIpThrottleThresholds($thresholds)
 * @method $this setUserThrottleInterval($interval)
 * @method $this setUserThrottleThresholds($thresholds)
 * @method int getGlobalThrottleInterval()
 * @method int|array getGlobalThrottleThresholds()
 * @method int getIpThrottleInterval()
 * @method int|array getIpThrottleThresholds()
 * @method int getUserThrottleInterval()
 * @method int|array getUserThrottleThresholds()
 * @method $this setRemindersExpiration(int $expiration)
 * @method $this setRemindersLottery(array $lottery)
 * @method $this setActivationsExpiration(int $expiration)
 * @method $this setActivationsLottery(array $lottery)
 * @method int getRemindersExpiration()
 * @method array getRemindersLottery()
 * @method int getActivationsExpiration()
 * @method array getActivationsLottery()
 * @method null|UserRepositoryInterface getUserRepository()
 * @method null|ActivationRepositoryInterface getActivationRepository()
 * @method null|PersistenceRepositoryInterface getPersistenceRepository()
 * @method null|ReminderRepositoryInterface getReminderRepository()
 * @method null|RoleRepositoryInterface getRoleRepository()
 * @method null|ThrottleRepositoryInterface getThrottleRepository()
 * @method $this setUserTable(string $table)
 * @method $this setActivationTable(string $table)
 * @method $this setPersistenceTable(string $table)
 * @method $this setReminderTable(string $table)
 * @method $this setRoleTable(string $table)
 * @method $this setThrottleTable(string $table)
 * @method string|null getUserTable()
 * @method string|null getActivationTable()
 * @method string|null getPersistenceTable()
 * @method string|null getReminderTable()
 * @method string|null getRoleTable()
 * @method string|null getThrottleTable()
 */
final class SecurityContextConfiguration
{
	/**
	 * Mapping of each entity to its EntityMapping class or object.
	 * @type array
	 */
	private $mappings = [
		'user'           => Mappings\UserMapping::class,
		'activation'     => Mappings\ActivationMapping::class,
		'permission'     => Mappings\PermissionCollectionMapping::class,
		'persistence'    => Mappings\PersistenceMapping::class,
		'reminder'       => Mappings\ReminderMapping::class,
		'role'           => Mappings\RoleMapping::class,
		'throttle'       => Mappings\ThrottleMapping::class,
		'globalThrottle' => Mappings\GlobalThrottleMapping::class,
		'ipThrottle'     => Mappings\IpThrottleMapping::class,
		'userThrottle'   => Mappings\UserThrottleMapping::class,
	];

	/**
	 * Mapping of entities to custom repositories.
	 *
	 * @type array
	 */
	private $repositories = [
		'user'        => null,
		'activation'  => null,
		'persistence' => null,
		'reminder'    => null,
		'role'        => null,
		'throttle'    => null
	];

	/**
	 * Single persistence flag.
	 *
	 * @type bool
	 */
	private $singlePersistence = false;

	/**
	 * Modules that allow disabling.
	 *
	 * @type array
	 */
	private $enabled = [
		'roles'       => true,
		'permissions' => true
	];

	/**
	 * Permissions configuration.
	 * Permission classes have to implement Sentinel's PermissionInterface. Sentinel ships
	 * with two: StandardPermissions and StrictPermissions.
	 *
	 * The permission repository is used to retrieve permissions based on route resources.
	 * Security ships with two: InsecurePermissionRepository and RoutePermissionRepository.
	 * @type array
	 */
	private $permissions = [
		'class'      => StandardPermissions::class,
		'repository' => InsecurePermissionRepository::class
	];

	/**
	 * Available checkpoints. Each checkpoint has to implement Sentinel's CheckpointInterface
	 * @type array
	 */
	private $checkpoints = [
		'throttle'   => ThrottleCheckpoint::class,
		'activation' => ActivationCheckpoint::class
	];

	/**
	 * Throttling configuration.
	 * Each throttling strategy can change the interval and set custom thresholds for
	 * each amount of retries.
	 *
	 * @type array
	 */
	private $throttles = [
		'global' => [
			'interval' => 900,
			'thresholds' => [
				10 => 1,
	            20 => 2,
	            30 => 4,
	            40 => 8,
	            50 => 16,
	            60 => 12
			]
		],
		'ip'     => [
			'interval' => 900,
			'thresholds' => 5
		],
		'user'   => [
			'interval' => 900,
			'thresholds' => 5
		]
	];

	/**
	 * Configuration of expiring modules: reminders and activations.
	 * Each of these modules may expire in a given time and has a lottery configuration,
	 * which Sentinel will use to sweep expired codes.
	 *
	 * @type array
	 */
	private $expiring = [
		'reminders' => [
			'expires' => 14400,
			'lottery' => [2,100]
		],
		'activations' => [
			'expires' => 259200,
			'lottery' => [2,100]
		]
	];

	/**
	 * Array of customized table names, one for each mapping.
	 *
	 * @type array
	 */
	private $customTables = [];

	/**
	 * Disable the throttling checkpoint
	 * @return $this
	 */
	public function disableThrottles()
	{
		unset($this->checkpoints['throttle']);

		return $this;
	}

	/**
	 * Enable throttling.
	 */
	public function enableThrottles()
	{
		$this->addCheckpoint('throttle', ThrottleCheckpoint::class);
	}

	/**
	 * @return bool
	 */
	public function isThrottlesEnabled()
	{
		return array_key_exists('throttle', $this->checkpoints);
	}

	/**
	 * @param string              $key
	 * @param CheckpointInterface $checkpoint
	 * @return $this
	 */
	public function addCheckpoint($key, CheckpointInterface $checkpoint)
	{
		$this->checkpoints[$key] = $checkpoint;

		return $this;
	}

	/**
	 * @param string $key
	 * @return $this
	 */
	public function removeCheckpoint($key)
	{
		unset($this->checkpoints[$key]);

		return $this;
	}

	/**
	 * @return array
	 */
	public function listCheckpoints()
	{
		return $this->checkpoints;
	}

	/**
	 * @param PermissionsInterface $permissionClass
	 * @return $this
	 */
	public function setPermissionClass(PermissionsInterface $permissionClass)
	{
		$this->permissions['class'] = $permissionClass;
		return $this;
	}

	/**
	 * @return PermissionsInterface
	 */
	public function getPermissionClass()
	{
		return $this->permissions['class'];
	}

	/**
	 * @param PermissionRepository $permissionRepository
	 * @return $this
	 */
	public function setPermissionRepository(PermissionRepository $permissionRepository)
	{
		$this->permissions['repository'] = $permissionRepository;
		return $this;
	}

	/**
	 * @return PermissionRepository
	 */
	public function getPermissionRepository()
	{
		return $this->permissions['repository'];
	}

	/**
	 * @return $this
	 */
	public function setSinglePersistence()
	{
		$this->singlePersistence = true;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function setMultiplePersistence()
	{
		$this->singlePersistence = false;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSinglePersistence()
	{
		return $this->singlePersistence;
	}

	/**
	 * @return bool
	 */
	public function isMultiplePersistence()
	{
		return ! $this->singlePersistence;
	}

	/**
	 * @param string        $entity
	 * @param EntityMapping $entityMapping
	 *
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	private function setMapping($entity, EntityMapping $entityMapping)
	{
		if (! array_key_exists($entity, $this->mappings))
		{
			throw new \InvalidArgumentException("'$entity' is not a valid mapping key. One of [" . implode(', ', array_keys($this->mappings)) . '] is expected.');
		}

		$this->mappings[$entity] = $entityMapping;
		return $this;
	}

	/**
	 * @param string $module
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	private function enable($module)
	{
		if (! array_key_exists($module, $this->enabled))
		{
			throw new \InvalidArgumentException("Module '$module' cannot be enabled or disabled. Only [" . implode(', ', array_keys($this->enabled)) . '] can.');
		}

		$this->enabled[$module] = true;

		return $this;
	}

	/**
	 * @param string $module
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	private function disable($module)
	{
		if (! array_key_exists($module, $this->enabled))
		{
			throw new \InvalidArgumentException("Module '$module' cannot be enabled or disabled. Only [" . implode(', ', array_keys($this->enabled)) . '] can.');
		}

		$this->enabled[$module] = false;

		return $this;
	}

	/**
	 * @param string $module
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	private function isEnabled($module)
	{
		if (! array_key_exists($module, $this->enabled))
		{
			throw new \InvalidArgumentException("Module '$module' cannot be enabled or disabled. Only [" . implode(', ', array_keys($this->enabled)) . '] can.');
		}

		return $this->enabled[$module];
	}

	/**
	 * @param string $entity
	 *
	 * @return EntityMapping|string
	 * @throws \InvalidArgumentException
	 */
	private function getMapping($entity)
	{
		if (! array_key_exists($entity, $this->mappings))
		{
			throw new \InvalidArgumentException("'$entity' is not a valid mapping key. One of [" . implode(', ', array_keys($this->mappings)) . '] is expected.');
		}

		return $this->mappings[$entity];
	}

	/**
	 * @param string $type
	 * @param string $key
	 * @param int|array $value
	 *
	 * @return $this
	 */
	private function setThrottle($type, $key, $value)
	{
		if (! isset($this->throttles[$type][$key]))
		{
			throw new \InvalidArgumentException("Invalid throttle type or parameter given.");
		}

		$this->throttles[$type][$key] = $value;
		return $this;
	}

	/**
	 * @param string $type
	 * @param string $key
	 *
	 * @return int|array
	 */
	private function getThrottle($type, $key)
	{
		if (! isset($this->throttles[$type][$key]))
		{
			throw new \InvalidArgumentException("Invalid throttle type or parameter given.");
		}

		return $this->throttles[$type][$key];
	}

	/**
	 * @param string $type
	 * @param string $subtype
	 * @param int|array $value
	 *
	 * @return $this
	 */
	private function setExpiring($type, $subtype, $value)
	{
		if (! isset($this->expiring[$type][$subtype]))
		{
			throw new \InvalidArgumentException("Invalid type or parameter given.");
		}

		$this->expiring[$type][$subtype] = $value;

		return $this;
	}

	/**
	 * @param string $type
	 * @param string $subtype
	 *
	 * @return int|array
	 */
	private function getExpiring($type, $subtype)
	{
		if (! isset($this->expiring[$type][$subtype]))
		{
			throw new \InvalidArgumentException("Invalid type or parameter given.");
		}

		return $this->expiring[$type][$subtype];
	}

	/**
	 * @param UserRepositoryInterface $userRepository
	 * @return $this
	 */
	public function setUserRepository(UserRepositoryInterface $userRepository)
	{
		$this->repositories['user'] = $userRepository;
		return $this;
	}

	/**
	 * @param ActivationRepositoryInterface $activationRepository
	 * @return $this
	 */
	public function setActivationRepository(ActivationRepositoryInterface $activationRepository)
	{
		$this->repositories['activation'] = $activationRepository;
		return $this;
	}

	/**
	 * @param PersistenceRepositoryInterface $persistenceRepository
	 * @return $this
	 */
	public function setPersistenceRepository(PersistenceRepositoryInterface $persistenceRepository)
	{
		$this->repositories['persistence'] = $persistenceRepository;
		return $this;
	}

	/**
	 * @param ReminderRepositoryInterface $reminderRepository
	 * @return $this
	 */
	public function setReminderRepository(ReminderRepositoryInterface $reminderRepository)
	{
		$this->repositories['reminder'] = $reminderRepository;
		return $this;
	}

	/**
	 * @param RoleRepositoryInterface $roleRepository
	 * @return $this
	 */
	public function setRoleRepository(RoleRepositoryInterface $roleRepository)
	{
		$this->repositories['role'] = $roleRepository;
		return $this;
	}

	/**
	 * @param ThrottleRepositoryInterface $throttleRepository
	 * @return $this
	 */
	public function setThrottleRepository(ThrottleRepositoryInterface $throttleRepository)
	{
		$this->repositories['throttle'] = $throttleRepository;
		return $this;
	}

	/**
	 * @param $entity
	 * @return mixed
	 */
	private function getRepository($entity)
	{
		if (! array_key_exists($entity, $this->repositories))
		{
			throw new \InvalidArgumentException("'$entity' is not a valid repository. One of [" . implode(', ', array_keys($this->repositories)) . '] is expected.');
		}

		return $this->repositories[$entity];
	}

	/**
	 * @param string $entity
	 * @param string $table
	 *
	 * @return $this
	 */
	private function setMappingTable($entity, $table)
	{
		$this->customTables[$entity] = $table;

		return $this;
	}

	/**
	 * @param string $entity
	 *
	 * @return string|null
	 */
	private function getMappingTable($entity)
	{
		return array_get($this->customTables, $entity);
	}

	/**
	 * @return array
	 */
	public function getMappings()
	{
		return $this->mappings;
	}

	/**
	 * is triggered when invoking inaccessible methods in an object context.
	 *
	 * @param $name      string
	 * @param $arguments array
	 *
	 * @return mixed
	 * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
	 */
	public function __call($name, $arguments)
	{
		if (preg_match('/^set(.*)Mapping$/', $name, $matches))
		{
			if (empty($arguments))
			{
				throw new \InvalidArgumentException("$name expects 1 parameter, none given.");
			}

			return $this->setMapping(lcfirst($matches[1]), array_shift($arguments));
		}

		if (preg_match('/^set(.*)Table$/', $name, $matches))
		{
			if (empty($arguments))
			{
				throw new \InvalidArgumentException("$name expects 1 parameter, none given.");
			}

			return $this->setMappingTable(lcfirst($matches[1]), array_shift($arguments));
		}

		if (preg_match('/^get(.*)Mapping$/', $name, $matches))
		{
			return $this->getMapping(lcfirst($matches[1]));
		}

		if (preg_match('/^get(.*)Mapping$/', $name, $matches))
		{
			return $this->getMappingTable(lcfirst($matches[1]));
		}

		if (preg_match('/^get(.*)Repository$/', $name, $matches))
		{
			return $this->getRepository(lcfirst($matches[1]));
		}

		if (preg_match('/^enable(.*)$/', $name, $matches))
		{
			return $this->enable(lcfirst($matches[1]));
		}

		if (preg_match('/^disable(.*)$/', $name, $matches))
		{
			return $this->disable(lcfirst($matches[1]));
		}

		if (preg_match('/^is(.*)Enabled$/', $name, $matches))
		{
			return $this->isEnabled(lcfirst($matches[1]));
		}

		if (preg_match('/^set(.*)Throttle(.*)$/', $name, $matches))
		{
			if (empty($arguments))
			{
				throw new \InvalidArgumentException("$name expects 1 parameter, none given.");
			}

			return $this->setThrottle(lcfirst($matches[1]), lcfirst($matches[2]), array_shift($arguments));
		}

		if (preg_match('/^get(.*)Throttle(.*)$/', $name, $matches))
		{
			return $this->getThrottle(lcfirst($matches[1]), lcfirst($matches[2]));
		}

		if (preg_match('/^set(.*)Expiration$/', $name, $matches))
		{
			if (empty($arguments))
			{
				throw new \InvalidArgumentException("$name expects 1 parameter, none given.");
			}

			return $this->setExpiring(lcfirst($matches[1]), 'expires', array_shift($arguments));
		}

		if (preg_match('/^set(.*)Lottery$/', $name, $matches))
		{
			if (empty($arguments))
			{
				throw new \InvalidArgumentException("$name expects 1 parameter, none given.");
			}

			return $this->setExpiring(lcfirst($matches[1]), 'lottery', array_shift($arguments));
		}

		if (preg_match('/^get(.*)Expiration$/', $name, $matches))
		{
			if (empty($arguments))
			{
				throw new \InvalidArgumentException("$name expects 1 parameter, none given.");
			}

			return $this->getExpiring(lcfirst($matches[1]), 'expires');
		}

		if (preg_match('/^get(.*)Lottery$/', $name, $matches))
		{
			if (empty($arguments))
			{
				throw new \InvalidArgumentException("$name expects 1 parameter, none given.");
			}

			return $this->getExpiring(lcfirst($matches[1]), 'lottery');
		}

		throw new \BadMethodCallException("Invalid method [$name].");
	}
}