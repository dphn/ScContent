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

use ScContent\Service\AbstractService,
    ScContent\Entity\ScUserInterface,
    ScContent\Exception\DomainException,
    ScContent\Exception\IoCException,
    //
    ZfcUser\Options\ModuleOptions as ZfcUserOptions,
    ZfcUser\Mapper\User as UserMapper,
    //
    Zend\Crypt\Password\Bcrypt,
    //
    Locale;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AccountService extends AbstractService
{
    /**
     * @var ZfcUser\Options\ModuleOptions
     */
    protected $zfcUserOptions;

    /**
     * @var ZfcUser\Mapper\User
     */
    protected $userMapper;

    /**
     * @param ZfcUser\Options\ModuleOptions $options
     * @return void
     */
    public function setZfcUserOptions(ZfcUserOptions $options)
    {
        $this->zfcUserOptions = $options;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ZfcUser\Options\ModuleOptions
     */
    public function getZfcUserOptions()
    {
        if (! $this->zfcUserOptions instanceof ZfcUserOptions) {
            throw new IoCException(
                'The ZfcUser module options was not set.'
            );
        }
        return $this->zfcUserOptions;
    }

    /**
     * @param ZfcUser\Mapper\User
     * @return void
     */
    public function setUserMapper(UserMapper $mapper)
    {
        $this->userMapper = $mapper;
    }

    /**
     * @throw ScContent\Exception\IoCException
     * @return ZfcUser\Mapper\User
     */
    public function getUserMapper()
    {
        if (! $this->userMapper instanceof UserMapper) {
            throw new IoCException(
                'The user mapper was not set.'
            );
        }
        return $this->userMapper;
    }

    /**
     * @throws ScContent\Exception\DomainException
     * @return ScContent\Entity\ScUserInterface
     */
    public function makeUserEntity()
    {
        $options = $this->getZfcUserOptions();
        $translator = $this->getTranslator();
        $class = $options->getUserEntityClass();
        if (! is_subclass_of($class, 'ScContent\Entity\ScUserInterface')) {
            throw new DomainException(sprintf(
                $translator->translate(
                    "The user entity class '%s' must inherit 'ScContent\Entity\ScUserInterface'"
                ),
                $class
            ));
        }
        $user  = new $class;
        return $user;
    }

    /**
     * @param ScContent\Entity\ScUserInterface
     * @return void
     */
    public function createAccount(ScUserInterface $user)
    {
        $eventManager = $this->getEventManager();
        $options = $this->getZfcUserOptions();
        $mapper = $this->getUserMapper();

        $bcrypt = new Bcrypt;
        $bcrypt->setCost($options->getPasswordCost());
        $user->setPassword($bcrypt->create($user->getPassword()));

        $mapper->insert($user);
        $eventManager->trigger(
            __FUNCTION__ . '.post',
            null,
            ['user' => $user]
        );
    }
}
