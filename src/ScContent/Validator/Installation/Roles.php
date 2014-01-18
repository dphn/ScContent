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
    ScContent\Exception\IoCException,
    //
    Zend\Validator\AbstractValidator;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Roles extends AbstractValidator
{
    /**
     * @var ScContent\Mapper\Installation\RolesMapper
     */
    protected $rolesMapper;

    /**
     * @param ScContent\Mapper\Installation\Roles
     * @return void
     */
    public function setMapper(RolesMapper $mapper)
    {
        $this->rolesMapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
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
    public function isValid($options)
    {
        if (empty($options)) {
            return true;
        }
        $mapper = $this->getMapper();

        $roles = [];
        foreach ($options as $option) {
            if (isset($option['role_id'])) {
                $roles[] = $option['role_id'];
            }
        }
        $registeredRoles = $mapper->findRegisteredRoles();
        $missingRoles = array_diff($roles, $registeredRoles);
        if (! empty($missingRoles)) {
            return false;
        }
        return true;
    }
}
