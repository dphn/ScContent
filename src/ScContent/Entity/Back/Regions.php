<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity\Back;

use ScContent\Entity\AbstractList,
    ScContent\Options\ModuleOptions,
    ScContent\Exception\InvalidArgumentException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Regions extends AbstractList
{
    /**
     * @var ScContent\Enity\Back\WidgetsList
     */
    protected $widgetsListPrototype;

    /**
     * @var array
     */
    protected $names = array();

    /**
     * @param ScContent\Options\ModuleOptions $options
     * @return void
     */
    public function __construct(ModuleOptions $options)
    {
        $theme = $options->getFrontendTheme();
        $regions = $theme['frontend']['regions'];
        foreach ($regions as $name => $region) {
            if (strtolower($name) != 'none') {
                $displayName = $name;
                if (isset($region['display_name'])) {
                    $displayName = $region['display_name'];
                }
                $this->names[$name] = $displayName;
                $widgetsList = clone($this->getWidgetsListPrototype());
                $this->items[$name] = $widgetsList;
            }
        }
        $this->names['none'] = 'Disabled';
        $widgetsList = clone($this->getWidgetsListPrototype());
        $this->items['none'] = $widgetsList;
    }

    /**
     * @param ScContent\Entity\WidgetItem $item
     * @throws ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function addItem(WidgetItem $item)
    {
        if (! $this->offsetExists($item->getRegion())) {
            throw new InvalidArgumentException(sprintf(
                "Unknown region '%s'.",
                $item->getRegion()
            ));
        }
        $items = &$this->items[$item->getRegion()];
        $items->addItem($item);
    }

    /**
     * @param mixed $index
     * @return boolean
     */
    public function offsetExists($index)
    {
        if (isset($this->items[$index])) {
            return true;
        }
        return false;
    }

    /**
     * @param string $name
     * @throws ScContent\Exception\InvalidArgumentException
     * @return string
     */
    public function getDisplayName($name)
    {
        if (! isset($this->names[$name])) {
            throw new InvalidArgumentException(sprintf(
                "Unknown region name '%s'.",
                $name
            ));
        }
        return $this->names[$name];
    }

    /**
     * @return array
     */
    public function getNames()
    {
        $names = $this->names;
        $names['none'] = '- None -';
        return $names;
    }

    /**
     * @return ScContent\Enity\Back\WidgetsList
     */
    protected function getWidgetsListPrototype()
    {
        if (! $this->widgetsListPrototype instanceof WidgetsList) {
            $this->widgetsListPrototype = new WidgetsList();
        }
        return $this->widgetsListPrototype;
    }
}
