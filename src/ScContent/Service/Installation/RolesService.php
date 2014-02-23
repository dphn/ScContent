<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Installation;

use ScContent\Mapper\RolesMapper,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class RolesService extends AbstractInstallationService
{
    /**
     * @var \ScContent\Mapper\RolesMapper
     */
    protected $rolesMapper;

    /**
     * @var null|array
     */
    protected $registeredRoles;

    /**
     * @param  \ScContent\Mapper\Installation\RolesMapper $mapper
     * @return void
     */
    public function setMapper(RolesMapper $mapper)
    {
        $this->rolesMapper = $mapper;
    }

    /**
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
     * @param  array $options
     * @return boolean
     */
    public function process($options)
    {
        $mapper = $this->getMapper();
        $registeredRoles = $this->getRegisteredRoles();

        if (! isset($options['role_id'])) {
            throw new InvalidArgumentException(
                "In the configuration of roles the 'role_id' option is not specified."
            );
        }
        if (! isset($options['route'])) {
            throw new InvalidArgumentException(
                "In the configuration of roles the option 'route' is not specified."
            );
        }

        if (in_array($options['role_id'], $registeredRoles)) {
            return true;
        }

        if (! array_key_exists('is_default', $options)) {
            $roleOptions['is_default'] = null;
        }
        if (! array_key_exists('parent_id', $options)) {
            $roleOptions['parent_id'] = null;
        }
        $mapper->addRole($options);

        return true;
    }

    /**
     * @return string[]
     */
    protected function getRegisteredRoles()
    {
        if (is_null($this->registeredRoles)) {
            $mapper = $this->getMapper();
            $this->registeredRoles = $mapper->findRegisteredRoles();
        }
        return $this->registeredRoles;
    }
}
