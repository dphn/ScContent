<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Installation;

use ScContent\Entity\Installation\DatabaseConfig,
    //
    Zend\Stdlib\Hydrator\HydratorInterface,
    Zend\Stdlib\Hydrator\ArraySerializable,
    Zend\Config\Config as Repository,
    Zend\Config\Writer\WriterInterface,
    Zend\Config\Writer\PhpArray,
    //
    PDO;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ConfigMapper
{
    /**
     * @var Zend\Config\Config
     */
    protected $repository;

    /**
     * @var Zend\Config\Writer\WriterInterface
     */
    protected $adapter;

    /**
     * @var Zend\Stdlib\Hydrator\HydratorInterface
     */
    protected $hydrator;

    /**
     * @param ScContent\Entity\Installation\DatabaseConfig $entity
     * @param string $source Source file path
     * @param string $destination Destination file path
     * @return void
     */
    public function save(DatabaseConfig $entity, $source, $destination)
    {
        $repository = $this->getRepository(require($source));
        $repository->sc->db = [];
        $repository->sc->db->driver = 'pdo';
        $data = $this->getHydrator()->extract($entity);
        if ('sqlite' == $data['driver']) {
            $repository->sc->db->dsn = 'sqlite:' . $data['path'];
        } else {
            $dsn = $data['driver']
                 . ':dbname=' . $data['database']
                 . ';host='   . $data['host'];

            if (version_compare(PHP_VERSION, '5.3.6') >= 0) {
                $dsn .= ';charset=utf8';
            }

            $repository->sc->db->dsn = $dsn;
            $repository->sc->db->username = $data['username'];
            $repository->sc->db->password = $data['password'];
            $repository->sc->db->hostname = $data['host'];
            /* @deprecated Current compatible PHP version >= 5.4
             *
             if (version_compare(PHP_VERSION, '5.3.6') < 0) {
                $repository->sc->db->driver_options = [];
                switch($data['driver']) {
                    case 'mysql':
                        $initCommand = PDO::MYSQL_ATTR_INIT_COMMAND;
                        $repository->sc->db->driver_options->$initCommand
                            = 'SET NAMES \'UTF8\'';
                        break;
                }
            }
            */
        }
        $adapter = $this->getAdapter();
        $adapter->toFile($destination, $repository);
    }

    /**
     * @param Zend\Config\Config $repository
     * @return void
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $array
     * @return Zend\Config\Config
     */
    public function getRepository($array)
    {
        if (! $this->repository instanceof Repository) {
            $this->repository = new Repository($array, true);
        }
        return $this->repository;
    }

    /**
     * @param Zend\Config\Writer\WriterInterface $adapter
     * @return void
     */
    public function setAdapter(WriterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return Zend\Config\Writer\WriterInterface
     */
    public function getAdapter()
    {
        if (! $this->adapter instanceof WriterInterface) {
            $this->adapter = new PhpArray();
        }
        return $this->adapter;
    }

    /**
     * @param Zend\Stdlib\Hydrator\HydratorInterface $hydrator
     * @return void
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    /**
     * @return Zend\Stdlib\Hydrator\HydratorInterface
     */
    public function getHydrator()
    {
        if (! $this->hydrator instanceof HydratorInterface) {
            $this->hydrator = new ArraySerializable();
        }
        return $this->hydrator;
    }
}
