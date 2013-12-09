<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Db;

use ScContent\Options\ModuleOptions,
    ScContent\Db\Resource,
    //
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ServiceManager\FactoryInterface,
    Zend\Db\Adapter\Driver\Pdo\Connection,
    Zend\Db\Adapter\Driver\Pdo\Pdo,
    Zend\Db\Adapter\Exception,
    Zend\Db\Adapter\Adapter;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AdapterServiceFactory implements FactoryInterface
{
    /**
     * @param Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return Zend\Db\Adapter\Adapter
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $moduleOptions = $serviceLocator->get('ScOptions.ModuleOptions');
        $connection = $this->makeConnection($moduleOptions);
        $driver = new Pdo($connection);

        $adapter = new Adapter($driver);
        return $adapter;
    }

    /**
     * @param ModuleOptions $moduleOptions
     * @throws Zend\Db\Adapter\Exception\InvalidConnectionParametersException
     * @throws Zend\Db\Adapter\Exception\RuntimeException
     * @return Zend\Db\Adapter\Driver\Pdo\Connection
     */
    protected function makeConnection(ModuleOptions $moduleOptions)
    {
        $dbOptions = $moduleOptions->getDb();

        $dsn = $username = $password = $hostname = $database = null;
        $options = [];
        foreach ($dbOptions as $key => $value) {
            switch (strtolower($key)) {
                case 'dsn':
                    $dsn = $value;
                    break;
                case 'driver':
                    $value = strtolower($value);
                    if (strpos($value, 'pdo') === 0) {
                        $pdoDriver = strtolower(
                            substr(
                                str_replace(['-', '_', ' '], '', $value), 3
                            )
                        );
                    }
                    break;
                case 'pdodriver':
                    $pdoDriver = (string) $value;
                    break;
                case 'user':
                case 'username':
                    $username = (string) $value;
                    break;
                case 'pass':
                case 'password':
                    $password = (string) $value;
                    break;
                case 'host':
                case 'hostname':
                    $hostname = (string) $value;
                    break;
                case 'port':
                    $port = (int) $value;
                    break;
                case 'database':
                case 'dbname':
                    $database = (string) $value;
                    break;
                case 'charset':
                    $charset = (string) $value;
                    break;
                case 'driver_options':
                case 'options':
                    $value = (array) $value;
                    $options = array_diff_key($options, $value) + $value;
                    break;
                default:
                    $options[$key] = $value;
                    break;
            }
        }
        if (! isset($dsn) && isset($pdoDriver)) {
            $dsn = [];
            switch ($pdoDriver) {
                case 'sqlite':
                    $dsn[] = $database;
                    break;
                default:
                    if(isset($database)) {
                        $dsn[] = "dbname={$database}";
                    }
                    if(isset($hostname)) {
                        $dsn[] = "host={$hostname}";
                    }
                    if(isset($port)) {
                        $dsn[] = "port={$port}";
                    }
                    if(isset($charset)) {
                        $dsn[] = "charset={$charset}";
                    }
                    break;
            }
            $dsn = $pdoDriver . ':' . implode(';', $dsn);
        } elseif (! isset($dsn)) {
            throw new Exception\InvalidConnectionParametersException(
                'A dsn was not provided or could not be constructed from your parameters',
                $dbOptions
            );
        }

        try {
            $resource = new Resource($dsn, $username, $password, $options);
            $resource->setAttribute(
                \PDO::ATTR_ERRMODE,
                \PDO::ERRMODE_EXCEPTION
            );
        } catch (\PDOException $e) {
            $code = $e->getCode();
            if (! is_long($code)) {
                $code = null;
            }
            throw new Exception\RuntimeException(
                'Connect Error: ' . $e->getMessage(),
                $code,
                $e
            );
        }
        $connection = new Connection($resource);
        return $connection;
    }
}
