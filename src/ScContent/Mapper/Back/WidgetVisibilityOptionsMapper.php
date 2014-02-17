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

use ScContent\Options\Back\WidgetVisibilityListOptions as Options,
    //
    Zend\Session\Container;

/**
 * Persistence the options of widget visibility editor.
 *
 * @author Dolphin <work.dolphin@gmail.com>
 */
class WidgetVisibilityOptionsMapper
{
    /**
     * @const string
     */
    const ContainerName = 'widget_visibility_options';

    /**
     * @param  array $query
     * @return \ScContent\Options\Back\WidgetVisibilityListOptions
     */
    public function getOptions($query)
    {
        $container = new Container(self::ContainerName);
        if (! isset($container['widget_id'])) {
            return new Options($query);
        }

        $storage = $container->getManager()->getStorage();
        if ($container['widget_id'] != $query['widget_id']) {
            $storage->clear(self::ContainerName);
            return new Options($query);
        }
        $options = new Options($storage[$container->getName()]);
        $options->exchangeArray($query);
        return $options;
    }

    /**
     * @param  \ScContent\Options\Back\WidgetVisibilityListOptions $options
     * @return WidgetVisibilityOptionsMapper
     */
    public function saveOptions(Options $options)
    {
        $container = new Container(self::ContainerName);
        $container->exchangeArray($options->getArrayCopy());
        return $this;
    }
}
