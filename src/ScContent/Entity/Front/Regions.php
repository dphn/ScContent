<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity\Front;

use ScContent\Entity\AbstractList,
    ScContent\Entity\WidgetsList,
    ScContent\Entity\WidgetInterface,
    ScContent\Options\ModuleOptionsInterface,
    ScContent\Exception\InvalidArgumentException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Regions extends AbstractList
{
    /**
     * @var ScContent\Enity\WidgetsList
     */
    protected $widgetsListPrototype;

    /**
     * @var array
     */
    protected $regionOptions = [];

    /**
     * @param array $theme
     */
    public function __construct(ModuleOptionsInterface $options)
    {
        $theme = $options->getFrontendTheme();
        if (! isset($theme['frontend']['regions'])) {
            throw new InvalidArgumentException(
                'Invalid format of the theme.'
            );
        }
        $regions = $theme['frontend']['regions'];
        foreach ($regions as $name => $region) {
            if (strtolower($name) == 'none') {
                continue;
            }
            $this->regionOptions[$name] = $region;
            $widgetsList = clone($this->getWidgetsListPrototype());
            $this->items[$name] = $widgetsList;
        }
    }

    /**
     * @param ScContent\Entity\WidgetInterface $item
     * @return void
     */
    public function addItem(WidgetInterface $item)
    {
        $items = &$this->items[$item->getRegion()];
        $items->addItem($item);
    }

    /**
     * @param string $regionName
     * @param string $optionName
     * @throws ScContent\Exception\InvalidArgumentException
     * @return mixed
     */
    public function getRegionOption($regionName, $optionName)
    {
        if (! isset($this->regionOptions[$regionName])) {
            throw new InvalidArgumentException(sprintf(
                "The region '%s' does not exists.",
                $regionName
            ));
        }
        if (! isset($this->regionOptions[$regionName][$optionName])) {
            throw new InvalidArgumentException(sprintf(
                "The option '%s' of region '%s' does not exists.",
                $regionName,
                $optionName
            ));
        }
        return $this->regionOptions[$regionName][$optionName];
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
     * @return ScContent\Enity\WidgetsList
     */
    protected function getWidgetsListPrototype()
    {
        if (! $this->widgetsListPrototype instanceof WidgetsList) {
            $this->widgetsListPrototype = new WidgetsList();
        }
        return $this->widgetsListPrototype;
    }
}
