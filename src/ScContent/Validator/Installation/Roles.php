<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Validator\Installation;

use ScContent\Mapper\RolesMapper,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException,
    //
    Zend\Validator\AbstractValidator;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Roles extends AbstractValidator
{
    /**
     * @var \ScContent\Mapper\Installation\RolesMapper
     */
    protected $rolesMapper;

    /**
     * @var null|array
     */
    protected $registeredRoles;

    /**
     * @param  \ScContent\Mapper\Installation\Roles
     * @return void
     */
    public function setMapper(RolesMapper $mapper)
    {
        $this->rolesMapper = $mapper;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Mapper\Installation\RolesMapper
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
     * @throws \ScContent\Exception\InvalidArgumentException
     * @return boolean
     */
    public function isValid($options)
    {
        if (empty($options)) {
            return false;
        }
        if (! isset($options['role_id'])) {
            throw new InvalidArgumentException(
                "Missing 'role_id' option."
            );
        }
        $roles = [];
        foreach ($options as $option) {
            if (isset($option['role_id'])) {
                $roles[] = $option['role_id'];
            }
        }
        $registeredRoles = $this->getRegisteredRoles();
        return in_array($options['role_id'], $registeredRoles);
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
