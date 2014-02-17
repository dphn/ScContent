<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Installation;

use ScContent\Service\Dir,
    ScContent\Entity\Installation\Credentials,
    ScContent\Exception\IoCException,
    //
    Zend\Config\Writer\PhpArray,
    Zend\Config\Config;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class CredentialsMapper
{
    /**
     * @var \ScContent\Service\Dir
     */
    protected $dir;

    /**
     * @var \Zend\Config\Writer\PhpArray
     */
    protected $writer;

    /**
     * @var string
     */
    protected $path = 'settings/installation.passwd.php';

    /**
     * @param  \ScContent\Service\Dir $dir
     * @return void
     */
    public function setDir(Dir $dir)
    {
        $this->dir = $dir;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Service\Dir
     */
    public function getDir()
    {
        if (! $this->dir instanceof Dir) {
            throw new IoCException(
                'The directory mapper was not set.'
            );
        }
        return $this->dir;
    }

    /**
     * @param \Zend\Config\Writer\PhpArray $writer
     */
    public function setWriter(PhpArray $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @return \Zend\Config\Writer\PhpArray
     */
    public function getWriter()
    {
        if (! $this->writer instanceof Writer) {
            $this->writer = new PhpArray();
        }
        return $this->writer;
    }

    /**
     * @param  string $path
     * @return void
     */
    public function setFilePath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function findCredentials()
    {
        $dir = $this->getDir();
        $file = $dir->module($this->getFilePath(), true);
        if (! $file) {
            return [];
        }
        $credentials = include $file;
        if (! isset($credentials['username'])
            || ! isset($credentials['password'])
        ) {
            return [];
        }
        return $credentials;
    }

    /**
     * @param  \ScContent\Entity\Installation\Credentials $credentials
     * @return void
     */
    public function save(Credentials $credentials)
    {
        $dir = $this->getDir();
        $file = $dir->module($dir->normalizePath($this->getFilePath()));
        $config = new Config($credentials->getArrayCopy());
        $writer = $this->getWriter();
        $writer->toFile($file, $config);
    }
}
