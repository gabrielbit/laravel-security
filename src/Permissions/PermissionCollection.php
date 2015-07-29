<?php namespace Digbang\Security\Permissions;

use Cartalyst\Sentinel\Permissions\PermissionsInterface;
use Cartalyst\Sentinel\Permissions\StandardPermissions;
use Cartalyst\Sentinel\Permissions\StrictPermissions;

/**
 * Class PermissionCollection
 * @package Digbang\Security\Permissions
 * I'll extend the StandardPermissions class to have class-scope access to the protected
 * properties of the internal PermissionsInterface implementation.
 */
class PermissionCollection extends StandardPermissions implements PermissionsInterface
{
	private $encoded;

	/**
	 * @type StandardPermissions|StrictPermissions
	 */
	private $permissionsImplementation;

	/**
	 * PermissionCollection constructor.
	 *
	 * @param StandardPermissions|StrictPermissions $permissionsImplementation
	 */
	public function __construct(PermissionsInterface $permissionsImplementation)
	{
		$this->permissionsImplementation = $permissionsImplementation;
		// Not calling parent.
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasAccess($permissions)
	{
		return $this->permissionsImplementation->hasAccess($permissions);
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasAnyAccess($permissions)
	{
		return $this->permissionsImplementation->hasAnyAccess($permissions);
	}

	/**
	 * Returns the secondary permissions.
	 *
	 * @return array
	 */
	public function getSecondaryPermissions()
	{

	}

	/**
	 * Sets secondary permissions.
	 *
	 * @param  array $secondaryPermissions
	 *
	 * @return void
	 */
	public function setSecondaryPermissions(array $secondaryPermissions)
	{
	}

	/**
	 * Lazily grab the prepared permissions.
	 *
	 * @return array
	 */
	protected function getPreparedPermissions()
	{
	}

	/**
	 * Does the heavy lifting of preparing permissions.
	 *
	 * @param  array $prepared
	 * @param  array $permissions
	 *
	 * @return void
	 */
	protected function preparePermissions(array &$prepared, array $permissions)
	{
	}

	/**
	 * Takes the given permission key and inspects it for a class & method. If
	 * it exists, methods may be comma-separated, e.g. Class@method1,method2.
	 *
	 * @param  string $key
	 *
	 * @return array
	 */
	protected function extractClassPermissions($key)
	{
	}

	/**
	 * Checks a permission in the prepared array, including wildcard checks and permissions.
	 *
	 * @param  array  $prepared
	 * @param  string $permission
	 *
	 * @return bool
	 */
	protected function checkPermission(array $prepared, $permission)
	{
	}

	/**
	 * {@inheritDoc}
	 */
	protected function createPreparedPermissions()
	{
		return parent::createPreparedPermissions(); // TODO: Change the autogenerated stub
	}

	protected function validateInstance()
	{
		if ($this->permissionsImplementation instanceof StandardPermissions ||
			$this->permissionsImplementation instanceof StrictPermissions)
		{
			return;
		}


	}
}
