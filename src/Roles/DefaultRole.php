<?php namespace Digbang\Security\Roles;

use Digbang\Doctrine\TimestampsTrait;

class DefaultRole implements Role
{
	use TimestampsTrait;
	use RoleTrait;

	/**
	 * @return \Carbon\Carbon
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	/**
	 * @return \Carbon\Carbon
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}

}
