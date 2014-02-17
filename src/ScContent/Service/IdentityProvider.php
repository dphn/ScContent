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
    BjyAuthorize\Provider\Identity\ProviderInterface,
    BjyAuthorize\Exception\InvalidRoleException,
    //
    ZfcUser\Service\User,
    //
    Zend\Permissions\Acl\Role\RoleInterface,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class IdentityProvider extends AbstractService implements ProviderInterface
{
    /**
     * @var \ScContent\Mapper\RolesMapper
     */
    protected $rolesMapper;

    /**
     * @var \ZfcUser\Service\User
     */
    protected $userService;

    /**
     * @var string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    protected $defaultRole = 'guest';

    /**
     * @var array
     */
    protected $identityRoles = [];

    /**
     * @param  \ZfcUser\Service\User $service
     * @return void
     */
    public function setUserService(User $service)
    {
        $this->userService = $service;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ZfcUser\Service\User
     */
    public function getUserService()
    {
        if (! $this->userService instanceof User) {
            throw new IoCException(
                'The user service was not set.'
            );
        }
        return $this->userService;
    }

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
    public function getIdentityRoles()
    {
        if (! empty($this->identityRoles)) {
            return $this->identityRoles;
        }
        $userService = $this->getUserService();
        $authService = $userService->getAuthService();
        if (! $authService->hasIdentity()) {
            $this->identityRoles = [$this->getDefaultRole()];
            return $this->identityRoles;
        }

        $userId = $authService->getIdentity()->getId();
        try {
            $mapper = $this->getMapper();
            $this->identityRoles = $mapper->findUserRoles($userId);
            return $this->identityRoles;
        } catch (Exception $e) {
            $events = $this->getEventManager();
            $events->trigger(
                ERROR,
                null,
                [
                    'file'      => __FILE__,
                    'class'     => __CLASS__,
                    'method'    => __METHOD__,
                    'line'      => __LINE__,
                    'exception' => $e
                ]
            );
            $this->identityRoles = [$this->getDefaultRole()];
            return $this->identityRoles;
        }
    }

    /**
     * @return string|\Zend\Permissions\Acl\Role\RoleInterface
     */
    public function getDefaultRole()
    {
        return $this->defaultRole;
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
