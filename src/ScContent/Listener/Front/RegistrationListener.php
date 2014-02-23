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

use ScContent\Listener\AbstractListener,
    ScContent\Mapper\RegistrationMapper,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\DebugException,
    ScContent\Exception\IoCException,
    //
    ZfcUser\Service\User,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class RegistrationListener extends AbstractListener
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
     * @throws \ScContent\Exception\InvalidArgumentException
     * @throws \ScContent\Exception\DebugException
     * @return void
     */
    public function update($event)
    {
        $user = $event->getParam('user');
        if (empty($user)) {
            throw new InvalidArgumentException(
                "Missing event param 'user'."
            );
        }
        $userId = $user->getId();

        $mapper = $this->getMapper();
        $mapper->beginTransaction();
        $tid = $mapper->getTransactionIdentifier();

        try {
            $mapper->registerUser($userId, $this->getRegistrationRole(), $tid);
            $mapper->commit();
        } catch (Exception $e) {
            $mapper->rollBack();
            $translator = $this->getTranslator();
            if (DEBUG_MODE) {
                throw new DebugException(
                    $translator->translate('Error: ') . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
        }
    }
}
