<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service;

use ScContent\Mapper\RolesMapper,
    ScContent\Exception\IoCException,
    //
    BjyAuthorize\Provider\Role\ProviderInterface,
    BjyAuthorize\Exception\InvalidRoleException,
    BjyAuthorize\Acl\Role;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class RoleProvider extends AbstractService implements ProviderInterface
{
    /**
     * @var \ScContent\Mapper\RolesMapper
     */
    protected $rolesMapper;

    /**
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole = 'guest';

    /**
     * @param  \ScContent\Mapper\RolesMapper $mapper
     * @return void
     */
    public function setMapper(RolesMapper $mapper)
    {
        $this->rolesMapper = $mapper;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Mapper\RolesMapper
     */
    public function getMapper()
    {
        if (! $this->rolesMapper instanceof RolesMapper) {
            throw new IoCException(
                'The roles mapper was not set.'
            );
        }
        return $this->rolesMapper;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        $mapper = $this->getMapper();
        $roles = [];

        $data = $mapper->findRoles();
        if (empty($data)) {
            return [$this->getDefaultRole()];
        }
        // Pass One: Build each object
        foreach ($data as $item) {
            $roleId = $item['role_id'];
            $roles[$roleId] = new Role($roleId, $item['parent_id']);
        }

        // Pass Two: Re-inject parent objects to preserve hierarchy
        /* @var $roleObj Role */
        foreach ($roles as $roleObj) {
            $parentRoleObj = $roleObj->getParent();

            if ($parentRoleObj && $parentRoleObj->getRoleId()) {
                $roleObj->setParent($roles[$parentRoleObj->getRoleId()]);
            }
        }

        return array_values($roles);
    }

    /**
     * @return \Zend\Permissions\Acl\Role\RoleInterface
     */
    public function getDefaultRole()
    {
        $role = $this->defaultRole;
        if (is_string($role)) {
            $role = new Role($role);
        }
        return $role;
    }

    /**
     * @param  string|\Zend\Permissions\Acl\Role\RoleInterface $defaultRole
     * @throws \BjyAuthorize\Exception\InvalidRoleException
     */
    public function setDefaultRole($defaultRole)
    {
        if (! ($defaultRole instanceof RoleInterface || is_string($defaultRole))) {
            throw InvalidRoleException::invalidRoleInstance($defaultRole);
        }
        $this->defaultRole = $defaultRole;
    }
}
