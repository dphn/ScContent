<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper;

use Zend\Db\Adapter\AdapterInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AbstractLayoutMapper extends AbstractDbMapper
{
    /**
     * @const string
     */
    const WidgetsTableAlias = 'widgetsalias';

    /**
     * @const string
     */
    const LayoutTableAlias = 'layoutalias';

    /**
     * @const string
     */
    const ContentTableAlias = 'contentalias';

    /**
     * @var array
     */
    protected $_tables = [
        self::WidgetsTableAlias => 'sc_widgets',
        self::LayoutTableAlias  => 'sc_layout',
        self::ContentTableAlias => 'sc_content',
    ];

    /**
     * @param \Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @return array
     */
    public function findExistingThemes()
    {
        $test = [];
        $select = $this->getSql()->select()
            ->from($this->getTable(self::LayoutTableAlias))
            ->columns(['theme'])
            ->group('theme');

        $result = $this->execute($select);
        return $this->toList($result, 'theme');
    }

    /**
     * @param  integer $id
     * @return null|array
     */
    public function findMetaById($id)
    {
        $select = $this->getSql()->select()
            ->from($this->getTable(self::LayoutTableAlias))
            ->columns([
                'id', 'theme', 'region', 'name', 'position',
            ])
            ->where(['id' => $id]);

        return $this->execute($select)->current();
    }
}
