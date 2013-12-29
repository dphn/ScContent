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

use ScContent\Options\ContentList as Options,
    ScContent\Exception\DomainException,
    //
    Zend\Session\Container,
    //
    Traversable;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListOptions
{
    /**
     * @param string $name
     * @param array $query
     * @param string $type optional
     * @throws ScContent\Exception\DomainException
     * @return ScContent\Options\ContentList
     */
    public function getOptons($name, $query = null, $type = '')
    {
        if (! Options::hasName($name)) {
            throw new DomainException(sprintf(
                "Unknown content list options identifier '%s'.",
                $name
            ));
        }
        $containerName = 'content_options_' . $name;
        $container = new Container($containerName);
        $storage = $container->getManager()->getStorage();

        $options = new Options($storage[$container->getName()]);
        $options->setName($name);

        if (! empty($type)) {
            $options->setType($type);
        }
        if (! is_array($query) && ! $query instanceof Traversable
            || ! array_key_exists('pane', $query)
            || $query['pane'] != $name
        ) {
            return $options;
        }

        if (empty($type) && array_key_exists('type', $query)) {
            $options->setType($query['type']);
        }
        if (array_key_exists('root', $query)) {
            $options->setRoot($query['root']);
        }
        if (array_key_exists('page', $query)) {
            $options->setPage($query['page']);
        }
        if (array_key_exists('filter', $query)) {
            $options->setFilter($query['filter']);
        }
        if (array_key_exists('order_by', $query)) {
            $options->setOrderBy($query['order_by']);
        }
        if (array_key_exists('parent', $query)) {
            $options->setParent($query['parent']);
        }
        return $options;
    }

    /**
     * @param ScContent\Options\ContentList $options
     * @return ScContent\Mapper\Back\ContentListOptions
     */
    public function saveOptions(Options $options)
    {
        $containerName = 'content_options_' . $options->getName();
        $container = new Container($containerName);
        $container->exchangeArray($options->getArrayCopy());
        return $this;
    }
}
