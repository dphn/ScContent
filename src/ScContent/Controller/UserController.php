<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller;

use ScContent\Service\IdentityProvider,
    ScContent\Service\RolesMapper,
    //
    Zend\Mvc\Controller\AbstractActionController;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class UserController extends AbstractActionController
{
    /**
     * @const string
     */
    const LoginRoute = 'zfcuser/login';

    /**
     * @const string
     */
    const LogoutRoute = 'zfcuser/logout';

    /**
     * @var ScContent\Mapper\RolesMapper
     */
    protected $rolesMapper;

    /**
     * @var ScContent\Service\IdentityProvider
     */
    protected $identityProvider;

    /**
     * @return Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        if (! $this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute(self::LoginRoute);
        }
        $rolesMapper = $this->getRolesMapper();
        $identityProvider = $this->getIdentityProvider();
        $roles = $identityProvider->getIdentityRoles();

        foreach ($roles as $role) {
            $route = $rolesMapper->findRouteForRole($role);
            if (! empty($route)) {
                return $this->redirect()->toRoute($route);
            }
        }
        return $this->redirect()->toRoute(self::LogoutRoute);
    }

    /**
     * @param ScContent\Mapper\RolesMapper $mapper
     * @return void
     */
    public function setRolesMapper(RolesMapper $mapper)
    {
        $this->rolesMapper = $mapper;
    }

    /**
     * @return ScContent\Mapper\RolesMapper
     */
    public function getRolesMapper()
    {
        if (! $this->rolesMapper instanceof RolesMapper) {
            $serviceLocator = $this->getServiceLocator();
            $this->rolesMapper = $serviceLocator->get(
                'ScMapper.Roles'
            );
        }
        return $this->rolesMapper;
    }

    /**
     * @param ScContent\Service\IdentityProvider $provider
     * @return void
     */
    public function setIdentityProvider(IdentityProvider $provider)
    {
        $this->identityProvider = $provider;
    }

    /**
     * @return ScContent\Service\IdentityProvider
     */
    public function getIdentityProvider()
    {
        if (! $this->identityProvider instanceof IdentityProvider) {
            $serviceLocator = $this->getServiceLocator();
            $this->identityProvider = $serviceLocator->get(
                'ScService.IdentityProvider'
            );
        }
        return $this->identityProvider;
    }
}
