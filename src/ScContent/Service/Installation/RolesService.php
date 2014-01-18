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
     * @var ScContent\Mapper\Installation\RolesMapper
     */
    protected $rolesMapper;

    /**
     * @param ScContent\Mapper\Installation\RolesMapper $mapper
     * @return void
     */
    public function setMapper(RolesMapper $mapper)
    {
        $this->rolesMapper = $mapper;
    }

    /**
     * @return ScContent\Mapper\Installation\RolesMapper
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
     * @param array $options
     * @return boolean
     */
    public function process($options)
    {
        $mapper = $this->getMapper();
        $registeredRoles = $mapper->findRegisteredRoles();
        foreach ($options as $i => &$roleOptions) {
            if (! isset($roleOptions['role_id'])) {
                throw new InvalidArgumentException(
                    "In the configuration of roles the 'role_id' option is not specified."
                );
            }
            if (in_array($roleOptions['role_id'], $registeredRoles)) {
                unset($options[$i]);
                continue;
            }
            if (! isset($roleOptions['route'])) {
                throw new InvalidArgumentException(
                    "In the configuration of roles the option 'route' is not specified."
                );
            }
            if (! array_key_exists('is_default', $roleOptions)) {
                $roleOptions['is_default'] = null;
            }
            if (! array_key_exists('parent_id', $roleOptions)) {
                $roleOptions['parent_id'] = null;
            }
            $mapper->addRole($roleOptions);
        }
        return true;
    }
}