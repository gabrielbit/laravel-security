<?php namespace Digbang\Security\Roles;

use Cartalyst\Sentinel\Roles\RoleRepositoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;

interface RoleRepository extends ObjectRepository, RoleRepositoryInterface
{

}