<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service;

use ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException,
    //
    ZfcBase\Module\AbstractModule,
    //
    Zend\Stdlib\ErrorHandler,
    //
    ReflectionClass;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Dir
{
    /**
     * @var string
     */
    protected $appAutoloadDir = '';

    /**
     * @var string
     */
    protected $appPublicDir = '';

    /**
     * @var string
     */
    protected $appUploadsDir = '';

    /**
     * @var string
     */
    protected $moduleDir = '';

    /**
     * Allows you to change the module.
     * The root directory of the module is calculated as the directory,
     * that contains the file of the Module class.
     *
     * Usage:
     * <code>
     *     $scDir = $serviceLocator->get('ScService.Dir');
     *     $dir = clone($scDir);
     *     $dir->setModule('MyModuleNamespace');
     *     $myModuleConfigFile = $dir->module('/config/module.config.php');
     *     $myModuleDataDir = $dir->module('/data');
     * </code>
     *
     * @api
     *
     * @param string $namespace
     * @throws ScContent\Exception\InvalidArgumentException
     * @return ScContent\Service\Dir
     */
    public function setModule($namespace)
    {
        $nsParts = explode('\\', $namespace);
        $moduleClass = $nsParts[0] . '\Module';

        if (! class_exists($moduleClass, true)) {
            throw new InvalidArgumentException(sprintf(
                "The Module class was not found in the '%s' namespace.",
                $namespace
            ));
        }

        $module = new $moduleClass();

        /* Recommended.
         * If you Module class extends 'ZfcBase\Module\AbstractModule'.
         */
        if ($module instanceof AbstractModule) {
            $this->moduleDir = realpath($module->getDir());
            return $this;
        }

        /* Otherwise - get a path using of Reflection.
         */
        $reflection = new ReflectionClass($module);

        $this->moduleDir = realpath(dirname($reflection->getFileName()));
        return $this;
    }

    /**
     * Returns the path to the root directory of the module or any
     * child path.
     *
     * The root directory of the module is calculated as the
     * directory, that contains the file of the Module class.
     * If the flag "checkIfExists" is set to TRUE, checks for the specified path
     * and if the path was not found, returns FALSE.
     *
     * Usage:
     * <code>
     *     $scDir = $serviceLocator->get('ScService.Dir');
     *     $dir = clone($scDir);
     *     $dir->setModule('MyModuleNamespace');
     *     $myModuleConfigFile = $dir->module('/config/module.config.php');
     *     $myModuleDataDir = $dir->module('/data');
     * </code>
     *
     * @api
     *
     * @param string  $path optional
     * @param boolean $checkIfExists optional default false
     * @return string | false
     */
    public function module($path = '', $checkIfExists = false)
    {
        $moduleDir = $this->moduleDir;
        if ($checkIfExists) {
            return realpath($moduleDir . DS . $path);
        }
        if (empty($path)) {
            return $moduleDir;
        }
        return $moduleDir . DS . $this->normalizePath($path);
    }

    /**
     * Returns the path to the root directory of the appliacation
     * or any child path.
     *
     * If the flag "checkIfExists" is set to TRUE, checks for the specified
     * path and if the path was not found, returns FALSE.
     *
     * @param string  $path optional
     * @param boolean $checkIfExists optional default false
     * @return string | false
     */
    public function app($path = '', $checkIfExists = false)
    {
        $appDir = getcwd();
        if ($checkIfExists) {
            return realpath($appDir . DS . $path);
        }
        if (empty($path)) {
            return $appDir;
        }
        return $appDir . DS . $this->normalizePath($path);
    }

    /**
     * Returns the path to the application autoload directory or any
     * child path.
     *
     * If the flag "checkIfExists" is set to TRUE, checks for the specified
     * path and if the path was not found, returns FALSE.
     *
     * Usage:
     * <code>
     *     $dir = $serviceLocator->get('ScService.Dir');
     *     $myConfigFile = $dir->appAutoload('my.local.php');
     * </code>
     *
     * @api
     *
     * @param string  $path
     * @param boolean $checkIfExists optional default false
     * @return string
     */
    public function appAutoload($path = '', $checkIfExists = false)
    {
        $appAutoloadDir = getcwd() . $this->getRelativeAppAutoloadDir();
        if ($checkIfExists) {
            return realpath($appAutoloadDir . DS . $path);
        }
        if (empty($path)) {
            return $appAutoloadDir;
        }
        return $appAutoloadDir . DS . $this->normalizePath($path);
    }

    /**
     * Returns the path to the application public directory or any
     * child path.
     *
     * If the flag "checkIfExists" is set to TRUE, checks for the specified
     * path and if the path was not found, returns FALSE.
     *
     * Usage:
     * <code>
     *     $dir = $serviceLocator->get('ScService.Dir');
     *     $myAssetsDir = $dir->appPublic('/mydirectory');
     *     $myCss = $dir->appPublic('/mydirectory/css/example.css');
     * </code>
     *
     * @api
     *
     * @param string  $path
     * @param boolean $checkIfExists optional default false
     * @return string
     */
    public function appPublic($path = '', $checkIfExists = false)
    {
        $appPublicDir = getcwd() . $this->getRelativeAppPublicDir();
        if ($checkIfExists) {
            return realpath($appPublicDir . DS . $path);
        }
        if (empty($path)) {
            return $appPublicDir;
        }
        return $appPublicDir . DS . $this->normalizePath($path);
    }

    /**
     * Returns the path to the upload directory, or any child path.
     *
     * If the flag "checkIfExists" is set to TRUE, checks for the specified
     * path and if the path was not found, returns FALSE.
     *
     * Usage:
     * <code>
     *     $dir = $serviceLocator->get('ScService.Dir');
     *     $uploadedFile = $dir->appUploads('test.jpg');
     * </code>
     *
     * @api
     *
     * @param string  $path
     * @param boolean $checkIfExists optional default false
     * @return string
     */
    public function appUploads($path = '', $checkIfExists = false)
    {
        $appUploadsDir = getcwd() . $this->getRelativeAppUploadsDir();
        if ($checkIfExists) {
            return realpath($appUploadsDir . DS . $path);
        }
        if (empty($path)) {
            return $appUploadsDir;
        }
        return $appUploadsDir . DS . $this->normalizePath($path);
    }

    /**
     * @param string $directory
     * @throws ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function setRelativeAppAutoloadDir($directory)
    {
        $appAutoloadDir = $this->normalizePath($directory);
        if (! realpath(getcwd() . DS . $appAutoloadDir)) {
            throw new InvalidArgumentException(sprintf(
                "The specified directory %s' does not exist.",
                $directory
            ));
        }
        $this->appAutoloadDir = $appAutoloadDir;
    }

    /**
     * Returns the relative path to the configuration autoload directory.
     *
     * @throws ScContent\Exception\IoCException
     * @return string
     */
    public function getRelativeAppAutoloadDir()
    {
        if (empty($this->appAutoloadDir)) {
            throw new IoCException(
                'The application configuration autoload directory was not set.'
            );
        }
        return DS . $this->appAutoloadDir;
    }

    /**
     * @param string $directory
     * @throws ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function setRelativeAppPublicDir($directory)
    {
        $appPublicDir = $this->normalizePath($directory);
        if (! realpath(getcwd() . DS . $appPublicDir)) {
            throw new InvalidArgumentException(sprintf(
                "The specified directory %s' does not exist.",
                $directory
            ));
        }
        $this->appPublicDir = $appPublicDir;
    }

    /**
     * Returns the relative path to the public directory.
     *
     * @throws ScContent\Exception\IoCException
     * @return string
     */
    public function getRelativeAppPublicDir()
    {
        if (empty($this->appPublicDir)) {
            throw new IoCException(
                'The application public directory was not set.'
            );
        }
        return DS . $this->appPublicDir;
    }

    /**
     * @param string $directory
     * @return void
     */
    public function setRelativeAppUploadsDir($directory)
    {
        $this->appUploadsDir = $this->normalizePath($directory);
    }

    /**
     * Returns relative path to application uploads directory.
     *
     * @throws ScContent\Exception\IoCException
     * @return string
     */
    public function getRelativeAppUploadsDir()
    {
        if (empty($this->appUploadsDir)) {
            throw new IoCException(
                'The application uploads directory was not set.'
            );
        }
        return DS . $this->appUploadsDir;
    }

    /**
     * @param string $path
     * @return string
     */
    public function normalizePath($path)
    {
        return trim(str_replace(['/', '\\'], DS, $path), DS);
    }
}
