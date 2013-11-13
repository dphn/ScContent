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

use ScContent\Service\Dir,
    ScContent\Exception\IoCException,
    //
    Zend\Validator\Exception\InvalidArgumentException,
    Zend\Validator\AbstractValidator;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Autoload extends AbstractValidator
{
    /**
     * @var ScContent\Service\Dir
     */
    protected $dir;

    /**
     * @param ScContent\Service\Dir $dir Directory service
     * @return void
     */
    public function setDir(Dir $dir)
    {
        $this->dir = $dir;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Service\Dir
     */
    public function getDir()
    {
        if (! $this->dir instanceof Dir) {
            throw new IoCException('The directory service was not set.');
        }
        return $this->dir;
    }

    /**
     * @param array $options
     * @throws Zend\Validator\Exception\InvalidArgumentException
     * @return boolean
     */
    public function isValid($options)
    {
        if (! isset($options['source_file'])) {
            throw new InvalidArgumentException(
                "Missing validation option 'source_file'."
            );
        }
        $dir = $this->getDir();
        $source = $dir->normalizePath($options['source_file']);
        $destination = basename($source, '.dist');
        return (bool) $dir->appAutoload($destination, true);
    }
}
