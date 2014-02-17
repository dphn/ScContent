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

use ScContent\Service\Dir,
    ScContent\Exception\IoCException,
    //
    Zend\Validator\Exception\InvalidArgumentException,
    Zend\Validator\AbstractValidator;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Assets extends AbstractValidator
{
    /**
     * @var \ScContent\Service\Dir
     */
    protected $dir;

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
            throw new IoCException('The directory service was not set.');
        }
        return $this->dir;
    }

    /**
     * @param  array $options
     * @throws \Zend\Validator\Exception\InvalidArgumentException
     * @return boolean
     */
    public function isValid($options)
    {
        if (! isset($options['validate_if_exists'])) {
            throw new InvalidArgumentException(
                "Missing validation option 'validate_if_exists'."
            );
        }
        if (! isset($options['version'])) {
            throw new InvalidArgumentException(
                "Missing validation option 'version'."
            );
        }
        $dir = $this->getDir();
        if (! $dir->appPublic($options['validate_if_exists'], true)) {
            return false;
        }
        $version = $options['validate_if_exists'] . DS . $options['version'];
        if (! $dir->appPublic($version, true)) {
            return false;
        }
        return true;
    }
}
