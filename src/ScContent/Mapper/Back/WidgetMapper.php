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

use ScContent\Entity\WidgetInterface,
    ScContent\Mapper\AbstractDbMapper,
    ScContent\Mapper\Exception\UnavailableSourceException,
    //
    Zend\Db\Adapter\AdapterInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetMapper extends AbstractDbMapper
{
    /**
     * @const string
     */
    const LayoutTableAlias = 'layoutalias';

    /**
     * @var array
     */
    protected $_tables = [
        self::LayoutTableAlias => 'sc_layout',
    ];

    /**
     * @param \Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @param  \ScContent\Entity\WidgetInterface $widget
     * @throws \ScContent\Mapper\Exception\UnavailableSourceException
     * @return void
     */
    public function find(WidgetInterface $widget)
    {
        $select = $this->getSql()->select()
            ->from($this->getTable(self::LayoutTableAlias))
            ->where([
                'id' => $widget->getId(),
            ]);

        $result = $this->execute($select)->current();
        if (empty($result)) {
            throw new UnavailableSourceException(
                'Widget was not found.'
            );
        }
        $hydrator = $this->getHydrator();
        $hydrator->hydrate($result, $widget);
    }

    /**
     * @param  \ScContent\Entity\WidgetInterface $widget
     * @return void
     */
    public function save(WidgetInterface $widget)
    {
        $hydrator = $this->getHydrator();
        $update = $this->getSql()->update()
            ->table($this->getTable(self::LayoutTableAlias))
            ->set($hydrator->extract($widget))
            ->where([
                'id' => $widget->getId()
            ]);

        $this->execute($update);
    }
}
