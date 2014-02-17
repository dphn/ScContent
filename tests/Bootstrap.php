<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContentTest;

use Zend\Authentication\AuthenticationService,
    Zend\Mvc\Service\ServiceManagerConfig,
    Zend\ServiceManager\ServiceManager,
    Zend\Loader\AutoloaderFactory,
    Zend\Stdlib\ArrayUtils,
    //
    RuntimeException;

if (0 > version_compare(phpversion(), '5.4.0')) {
    exit(
        'The module ScContent need PHP version >= 5.4.0' . PHP_EOL
    );
}

error_reporting(E_ALL | E_STRICT);
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
chdir(__DIR__);

class Bootstrap
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected static $serviceManager;

    /**
     * @var array
     */
    protected static $config;

    /**
     * @return void
     */
    public static function init()
    {
        if (is_readable(__DIR__ . DS . 'TestConfig.php')) {
            $testConfig = include __DIR__ . DS . 'TestConfig.php';
        } else {
            $testConfig = include __DIR__ . DS . 'TestConfig.php.dist';
        }

        $zf2ModulePaths = [];

        if (isset($testConfig['module_listener_options']['module_paths'])) {
            $modulePaths = $testConfig['module_listener_options']['module_paths'];
            foreach ($modulePaths as $modulePath) {
                if (($path = static::findParentPath($modulePath)) ) {
                    $zf2ModulePaths[] = $path;
                }
            }
        }

        $zf2ModulePaths  = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
        $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS')
                         ? ''
                         : (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');

        static::initAutoloader();

        // use ModuleManager to load this module and it's dependencies
        $baseConfig = [
            'module_listener_options' => [
                'module_paths' => explode(PATH_SEPARATOR, $zf2ModulePaths),
            ],
        ];

        $config = ArrayUtils::merge($baseConfig, $testConfig);

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();

        // ZfcUser
        $serviceManager->setService('zfcuser_auth_service', new AuthenticationService());

        static::$serviceManager = $serviceManager;
        static::$config = $config;
    }

    /**
     * @return \Zend\ServiceManager\ServiceManager
     */
    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    /**
     * @return array
     */
    public static function getConfig()
    {
        return static::$config;
    }

    /**
     * @throws \RuntimeException
     * @return void
     */
    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        $zf2Path = getenv('ZF2_PATH');
        if (!$zf2Path) {
            if (defined('ZF2_PATH')) {
                $zf2Path = ZF2_PATH;
            } elseif (is_dir($vendorPath . str_replace('/', DS, '/ZF2/library'))) {
                $zf2Path = $vendorPath . str_replace('/', DS, '/ZF2/library');
            } elseif (is_dir($vendorPath . str_replace('/', DS, '/zendframework/zendframework/library'))) {
                $zf2Path = $vendorPath . str_replace('/', DS, '/zendframework/zendframework/library');
            }
        }

        if (! $zf2Path) {
            throw new RuntimeException(
                'Unable to load ZF2. Run `php composer.phar install` or'
                . ' define a ZF2_PATH environment variable.'
            );
        }

        if (file_exists($vendorPath . DS . 'autoload.php')) {
            include $vendorPath . DS . 'autoload.php';
        }

        include $zf2Path . str_replace('/', DS, '/Zend/Loader/AutoloaderFactory.php');

        AutoloaderFactory::factory([
            'Zend\Loader\StandardAutoloader' => [
                'autoregister_zf' => true,
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . DS . __NAMESPACE__,
                ],
            ],
        ]);
    }

    /**
     * @param  string $path
     * @return boolean|string
     */
    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . DS . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) return false;
            $previousDir = $dir;
        }
        return $dir . DS . $path;
    }
}

Bootstrap::init();
