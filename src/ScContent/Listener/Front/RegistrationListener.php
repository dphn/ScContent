<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Front;

use ScContent\Mapper\RegistrationMapper,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException,
    //
    ZfcUser\Service\User,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class RegistrationListener
{
    /**
     * @var \ScContent\Mapper\Front\RegistrationMapper
     */
    protected $registrationMapper;

    /**
     * @var string
     */
    protected $registrationRole = 'user';

    /**
     * @param  \ScContent\Mapper\RegistrationMapper $mapper
     * @return void
     */
    public function setMapper($mapper)
    {
        $this->registrationMapper = $mapper;
    }

    /**
     * @throw  \ScContent\Exception\IoCException
     * @return \ScContent\Mapper\RegistrationMapper
     */
    public function getMapper()
    {
        if (! $this->registrationMapper instanceof RegistrationMapper) {
            throw new IoCException(
                'The registration mapper was not set.'
            );
        }
        return $this->registrationMapper;
    }

    /**
     * @param string $role
     */
    public function setRegistrationRole($role)
    {
        $this->registrationRole = $role;
    }

    /**
     * @return string
     */
    public function getRegistrationRole()
    {
        return $this->registrationRole;
    }

    /**
     * @throw  \ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function update($event)
    {
        $user = $event->getParam('user');
        if (empty($event)) {
            throw new InvalidArgumentException(
                "Missing event param 'user'."
            );
        }
        $userId = $user->getId();

        $mapper = $this->getMapper();
        try {
            $mapper->registerUser($userId, $this->getRegistrationRole());
        } catch (Exception $e) {
            // unset all session data
            if (! session_id()) {
                session_start();
            }
            session_unset();
            session_destroy();
            $mapper->remove($userId);
        }
    }
}
