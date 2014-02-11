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

use ScContent\Mapper\Installation\CredentialsMapper,
    ScContent\Exception\IoCException,
    //
    Zend\Authentication\Exception\RuntimeException,
    Zend\Authentication\Adapter\AdapterInterface,
    Zend\Authentication\Result,
    Zend\Crypt\Password\Bcrypt,
    Zend\Http\Request;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AuthenticationAdapter implements AdapterInterface
{
    /**
     * @const integer
     */
    const BcryptCost = 10;

    /**
     * @var Zend\Http\Request
     */
    protected $request;

    /**
     * @var ScContent\Mapper\Installation\CredentialsMapper
     */
    protected $mapper;

    /**
     * @var string
     */
    protected $username = '';

    /**
     * @var string
     */
    protected $password = '';

    /**
     * @param Zend\Http\Request $request
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return Zend\Http\Request
     */
    public function getRequest()
    {
        if (! $this->request instanceof Request) {
            throw new IoCException(
                'Request was not set.'
            );
        }
        return $this->request;
    }

    /**
     * @param ScContent\Mapper\Installation\CredentialsMapper $mapper
     * @return void
     */
    public function setMapper(CredentialsMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Installation\CredentialsMapper
     */
    public function getMapper()
    {
        if (! $this->mapper instanceof CredentialsMapper) {
            throw new IoCException(
                'The mapper was not set.'
            );
        }
        return $this->mapper;
    }

    /**
     * @param string $name
     */
    public function setUsername($name)
    {
        $this->username = $name;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        $username = $this->username;
        if (empty($username)) {
            $request = $this->getRequest();
            $username = $request->getPost('username', '');
        }
        return $username;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        $password = $this->password;
        if (empty($password)) {
            $request = $this->getRequest();
            $password = $request->getPost('password', '');
        }
        return $password;
    }

    /**
     * @throws Zend\Authentication\Exception\RuntimeException
     * @return Zend\Authentication\Result
     */
    public function authenticate()
    {
        $username = $this->getUsername();
        if (empty($username)) {
            throw new RuntimeException(
                'Username was not set.'
            );
        }

        $password = $this->getPassword();
        if (empty($password)) {
            throw new RuntimeException(
                'Password was not set.'
            );
        }

        $mapper = $this->getMapper();
        $credentials = $mapper->findCredentials();

        if (empty($credentials)) {
            throw new RuntimeException(
                'Credentials was not found.'
            );
        }

        $bcrypt = new Bcrypt();
        $bcrypt->setCost(self::BcryptCost);

        $verification = $bcrypt->verify($username, $credentials['username']);
        if (! $verification) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, []);
        }

        $verification = $bcrypt->verify($password, $credentials['password']);
        if (! $verification) {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, []);
        }

        return new Result(Result::SUCCESS, ['username' => $username]);
    }
}
