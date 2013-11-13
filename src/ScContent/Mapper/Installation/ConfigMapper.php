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
    ScContent\Service\Dir,
    //
    ScContent\Mapper\Exception\RuntimeException,
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
     * @var ScContent\Service\Dir
     */
    protected $dir;

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
     * Constructor
     *
     * @param ScContent\Service\Dir $dir
     */
    public function __construct(Dir $dir)
    {
        $this->dir = $dir;
    }

    /**
     * @param ScContent\Entity\Installation\DatabaseConfig $entity
     * @param string $source
     * @throws ScContent\Mapper\Exception\RuntimeException
     * @return void
     */
    public function save(DatabaseConfig $entity, $source)
    {
        $dir = $this->dir;
        if (! $dir->module($source, true)) {
            throw new RuntimeException(sprintf(
                "Unable to install configuration. Missing source file '%s'.",
                $source
            ));
        }
        $destination = $dir->appAutoload(basename($source, '.dist'));
        $repository = $this->getRepository(
            require($dir->module($source))
        );
        $repository->sc->db = array();
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
            if (version_compare(PHP_VERSION, '5.3.6') < 0) {
                $repository->sc->db->driver_options = array();
                switch($data['driver']) {
                    case 'mysql':
                        $initCommand = PDO::MYSQL_ATTR_INIT_COMMAND;
                        $repository->sc->db->driver_options->$initCommand
                            = 'SET NAMES \'UTF8\'';
                        break;
                }
            }
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
