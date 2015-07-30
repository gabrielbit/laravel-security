<?php namespace Digbang\Security\Mappings;

use Digbang\Doctrine\Metadata\Builder;
use Digbang\Doctrine\Metadata\Relations\HasMany;
use Digbang\Security\Activations\DefaultActivation;
use Digbang\Security\Permissions\UserPermission;
use Digbang\Security\Roles\DefaultRole;
use Digbang\Security\Persistences\DefaultPersistence;
use Digbang\Security\Reminders\DefaultReminder;
use Digbang\Security\Throttling\DefaultThrottle;

trait UserMappingTrait
{
	protected $enabled = [
		'roles' => true,
		'throttles' => true,
		'permissions' => true
	];

	/**
	 * Needed for inverse mapping of hasMany relations.
	 *
	 * @type string
	 */
	protected $name = 'user';

	/**
	 * Relations mapping. Override this with your custom objects if needed.
	 * Each relation value needs a FQCN in position 0 and a field name in position 1.
	 *
	 * IMPORTANT: Relations will NOT be iterated! Each object will access its specific
	 *            relation keys.
	 *
	 * @type array
	 */
	protected $relations = [
		'roles'        => [DefaultRole::class,           'roles'],
		'persistences' => [DefaultPersistence::class,    'persistences'],
		'activations'  => [DefaultActivation::class,     'activations'],
		'reminders'    => [DefaultReminder::class,       'reminders'],
		'throttles'    => [DefaultThrottle::class,       'throttles'],
		'permissions'  => [UserPermission::class, 'permissions'],
	];

	/**
	 * Adds all mappings: properties and relations
	 *
	 * @param Builder $builder
	 */
	public function addMappings(Builder $builder)
	{
		$this->addProperties($builder);
		$this->addRelations($builder);
	}

	/**
	 * Adds only properties
	 *
	 * @param Builder $builder
	 */
	public function addProperties(Builder $builder)
	{
		$builder
			->primary()
			->uniqueString('email')
			->string('password')
			->nullableDatetime('lastLogin')
			->nullableString('firstName')
			->nullableString('lastName')
			->timestamps();
	}

	/**
	 * Adds only relations
	 *
	 * @param Builder $builder
	 */
	public function addRelations(Builder $builder)
	{
		$this
			->hasMany('persistences', $builder)
			->hasMany('activations',  $builder)
			->hasMany('reminders',    $builder);

		if ($this->enabled['throttles'])
		{
			$this->hasMany('throttles', $builder);
		}

		if ($this->enabled['roles'])
		{
			$builder->belongsToMany($this->relations['roles'][0], $this->relations['roles'][1]);
		}

		if ($this->enabled['permissions'])
		{
			$this->hasMany('permissions', $builder);
		}
	}

	/**
	 * Disable the roles relation.
	 * @return void
	 */
	public function disableRoles()
	{
		$this->enabled['roles'] = false;
	}

	/**
	 * Disable the throttles relation.
	 * @return void
	 */
	public function disableThrottles()
	{
		$this->enabled['throttles'] = false;
	}

	/**
	 * Disable the permissions relation.
	 * @return void
	 */
	public function disablePermissions()
	{
		$this->enabled['permissions'] = false;
	}

	/**
	 * @param string  $key
	 * @param Builder $builder
	 * @return $this
	 */
	private function hasMany($key, Builder $builder)
	{
		$builder->hasMany($this->relations[$key][0], $this->relations[$key][1], function(HasMany $hasMany){
			$hasMany->mappedBy($this->name);
		});

		return $this;
	}
}
