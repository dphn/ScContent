<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Validator\Installation;

use ScContent\Mapper\RolesMapper,
    ScContent\Exception\IoCException,
    //
    Zend\Validator\AbstractValidator,
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Account extends AbstractValidator
{
    /**
     * @var ScContent\Mapper\RolesMapper
     */
    protected $rolesMapper;

    /**
     * @var string
     */
    protected $adminRole = 'admin';

    /**
     * @param ScContent\Mapper\RolesMapper $mapper
     * @return void
     */
    public function setMapper(RolesMapper $mapper)
    {
        $this->rolesMapper = $mapper;
    }

    /**
     * @return ScContent\Mapper\RolesMapper
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
     * @param string $role
     * @return void
     */
    public function setAdminRole($role)
    {
        $this->adminRole = $role;
    }

    /**
     * @return string
     */
    public function getAdminRole()
    {
        return $this->adminRole;
    }

    /**
     * @param null $options Not used
     * @return boolean
     */
    public function isValid($options = null)
    {
        $mapper = $this->getMapper();
        $total = $mapper->getAccountsCount($this->getAdminRole());
        //var_dump($total); exit();
        if ($total > 0) {
            return true;
        }
        return false;
    }
}
