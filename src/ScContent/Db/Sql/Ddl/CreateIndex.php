<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Db\Sql\Ddl;

use ScContent\Exception\InvalidArgumentException,
    //
    Zend\Db\Adapter\Platform\PlatformInterface,
    Zend\Db\Sql\Ddl\SqlInterface,
    Zend\Db\Sql\AbstractSql;

class CreateIndex extends AbstractSql implements SqlInterface
{
    /**
     * @const string
     */
    const Index = 'index';
    const Table = 'table';
    const Columns = 'columns';

    /**
     * @const string
     */
    const Key   = 'key';

    /**
     * @const string
     */
    const Unique = 'unique';

    /**
     * @const string
     */
    const Primary = 'primary';

    /**
     * @const string
     */
    const Fulltext = 'fulltext';

    /**
     * @var array
     */
    protected $types = [
        self::Key      => '',
        self::Unique   => 'UNIQUE',
        self::Primary  => 'PRIMARY',
        // FULLTEXT | CLUSTERED | ''
        self::Fulltext => 'FULLTEXT',
    ];

    /**
     * CREATE %1$s INDEX %2$s ON %3$s (%4$s)
     *
     * @var array
    */
    protected $specifications = [
        self::Index    => 'CREATE %s INDEX %s',
        self::Table    => 'ON %s',
        self::Columns  => [
            '(%1$s)' => [
                [1 => '%1$s', 'combinedby' => ', ']
            ]
        ],
    ];

    /**
     * @var string
    */
    protected $type = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $table = '';

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @param string $table
     * @param string $type
     * @param string $name
     * @param null | string | array $columns
    */
    public function __construct
    (
        $table = '',
        $type = '',
        $name = '',
        $columns = null
    ) {
        (! $columns) ?  : $this->setColumns($columns);
        (! $table)   ?  : $this->setTable($table);
        (! $type)    ?  : $this->setType($type);
        (! $name)    ?  : $this->setName($name);
    }

    /**
     * @param string $table
     * @return CreateIndex
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param string $type
     * @throws ScContent\Exception\InvalidArgumentException
     * @return CreateIndex
     */
    public function setType($type)
    {
        if (! array_key_exists($type, $this->types)) {
            throw new InvalidArgumentException(sprintf(
                "Unknown index type '%s'."
            ));
        }
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getRawType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->types[$this->getRawType()];
    }

    /**
     * @param string $name
     * @return CreateIndex
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null | string | array $columns $columns
     * @return CreateIndex
     */
    public function setColumns($columns)
    {
        if (! is_array($columns)) {
            $columns = [$columns];
        }

        $this->columns = $columns;
        return $this;
    }

    /**
     * @param  string $column
     * @return CreateIndex
     */
    public function addColumn($column)
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param  PlatformInterface $adapterPlatform
     * @return string
     */
    public function getSqlString(PlatformInterface $adapterPlatform = null)
    {
        // get platform, or create default
        $adapterPlatform = ($adapterPlatform) ?: new AdapterSql92Platform;

        $sqls       = [];
        $parameters = [];

        foreach ($this->specifications as $name => $specification) {
            $parameters[$name] = $this->{'process' . $name}(
                $adapterPlatform,
                null,
                null,
                $sqls,
                $parameters
            );

            if ($specification && is_array($parameters[$name])) {
                $sqls[$name] = $this->createSqlFromSpecificationAndParameters(
                    $specification,
                    $parameters[$name]
                );
            }
        }

        $sql = implode(' ', $sqls);

        return $sql;
    }

    /**
     * @param Zend\Db\Adapter\Platform\PlatformInterface $adapterPlatform
     * @return array
     */
    protected function processIndex(PlatformInterface $adapterPlatform = null)
    {
        $sqls = [
            ! $this->type ? '' : $this->types[$this->type],
            ! $this->name ? '' : $adapterPlatform->quoteIdentifier($this->name),
        ];
        return $sqls;
    }

    /**
     * @param Zend\Db\Adapter\Platform\PlatformInterface $adapterPlatform
     * @return array
     */
    protected function processTable(PlatformInterface $adapterPlatform = null)
    {
        return [$adapterPlatform->quoteIdentifier($this->table)];
    }

    /**
     * @param Zend\Db\Adapter\Platform\PlatformInterface $adapterPlatform
     * @return array
     */
    protected function processColumns(PlatformInterface $adapterPlatform = null)
    {
        $sqls = [];
        foreach ($this->columns as $column) {
            $sqls[] = $adapterPlatform->quoteIdentifier($column);
        }
        return [$sqls];
    }
}
