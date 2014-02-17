<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Back;

use ScContent\Service\Dir,
    ScContent\Exception\RuntimeException,
    //
    Zend\Config\Writer\PhpArray,
    Zend\Config\Config;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class SettingsMapper
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
     * @var \Zend\Config\Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $settingsFilePath = 'settings/module.settings.php';

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
                'The directory service was not set.'
            );
        }
        return $this->dir;
    }

    /**
     * @param  \Zend\Config\Writer\PhpArray $writer
     * @return void
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
        if (! $this->writer instanceof PhpArray) {
            $this->writer = new PhpArray();
        }
        return $this->writer;
    }

    /**
     * @param  \Zend\Config\Config $config
     * @return void
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return \Zend\Config\Config
     */
    public function getConfig()
    {
        if (! $this->config instanceof Config) {
            $dir = $this->getDir();
            $path = $dir->module($this->getSettingsFilePath(), true);
            if (! $path) {
                throw new RuntimeException(
                    "Module settings file '%s' was not found.",
                    $this->getSettingsFilePath()
                );
            }
            $this->config = new Config(require($path), true);
        }
        return $this->config;
    }

    /**
     * @param  string $path
     * @return string
     */
    public function setSettingsFilePath($path)
    {
        $this->settingsFilePath = $path;
    }

    /**
     * @return string
     */
    public function getSettingsFilePath()
    {
        return $this->settingsFilePath;
    }

    /**
     * @param  \Zend\Config\Config $config
     * @return void
     */
    public function saveConfig(Config $config)
    {
        $dir = $this->getDir();
        $writer = $this->getWriter();
        $path = $dir->module($this->getSettingsFilePath(), true);
        if (! $path) {
            throw new RuntimeException(
                "Module settings file '%s' was not found.",
                $this->getSettingsFilePath()
            );
        }
        $writer->toFile($path, $config);
    }
}
