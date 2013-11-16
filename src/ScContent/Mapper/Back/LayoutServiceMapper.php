<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Back;

use ScContent\Mapper\AbstractLayoutMapper,
    ScContent\Options\ModuleOptions,
    ScContent\Entity\Back\Regions,
    ScContent\Entity\Back\WidgetItem,
    //
    Zend\Db\Adapter\AdapterInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LayoutServiceMapper extends AbstractLayoutMapper
{
    /**
     * @var ScContent\Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * Constructor
     *
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     * @param ScContent\Options\ModuleOptions  $options
     */
    public function __construct(
        AdapterInterface $adapter,
        ModuleOptions $options
    ) {
        $this->setAdapter($adapter);
        $this->moduleOptions = $options;
    }

    /**
     * @param string $theme
     * @return ScContent\Entity\Back\Regions
     */
    public function findRegions($theme)
    {
        $moduleOptions = $this->moduleOptions;
        $list = new Regions($moduleOptions);
        $widgets = $moduleOptions->getWidgets();
        if (! is_array($widgets)) {
            return $list;
        }
        $theme = $moduleOptions->getFrontendTheme();
        $themeName = $moduleOptions->getFrontendThemeName();
        if (! isset($theme['frontend']['regions'])
            || ! is_array($theme['frontend']['regions'])
         ) {
            return $list;
        }
        $regions = $theme['frontend']['regions'];
        $availableRegions = array_keys($regions);
        if (! in_array('none', $availableRegions)) {
            $availableRegions[] = 'none';
        }
        $availableWidgets = array_keys($widgets);

        $select = $this->getSql()
            ->select()
            ->from($this->getTable(self::LayoutTableAlias))
            ->where(array(
                'theme'  => $themeName,
                'region' => $availableRegions,
                'name'   => $availableWidgets,
            ))
            ->order(array('region ASC', 'position ASC'));

        $results = $this->execute($select);

        $hydrator = $this->getHydrator();
        $itemPrototype = new WidgetItem();
        foreach ($results as $result) {
            $item = clone ($itemPrototype);
            $hydrator->hydrate($result, $item);
            $list->addItem($item);
        }
        return $list;
    }
}
