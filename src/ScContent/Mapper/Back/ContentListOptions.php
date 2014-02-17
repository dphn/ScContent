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

use ScContent\Options\Back\ContentListOptions as Options,
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
     * @const string
     */
    const ContainerPrefix = 'content_options_';

    /**
     * @param  string $name
     * @param  array $query
     * @param  string $type optional
     * @throws \ScContent\Exception\DomainException
     * @return \ScContent\Options\Back\ContentListOptions
     */
    public function getOptons($name, $query = null, $type = '')
    {
        if (! Options::hasName($name)) {
            throw new DomainException(sprintf(
                "Unknown content list options identifier '%s'.",
                $name
            ));
        }
        $containerName = self::ContainerPrefix . $name;
        $container = new Container($containerName);
        $storage = $container->getManager()->getStorage();

        $options = new Options($storage[$container->getName()]);
        $options->setName($name);

        if (! empty($type)) {
            $options->setType($type);
            if (isset($query['type'])) {
                unset($query['type']);
            }
        }
        if (! is_array($query) && ! $query instanceof Traversable
            || ! array_key_exists('pane', $query)
            || $query['pane'] != $name
        ) {
            return $options;
        }
        $options->exchangeArray($query);
        return $options;
    }

    /**
     * @param  \ScContent\Options\Back\ContentListOptions $options
     * @return \ScContent\Mapper\Back\ContentListOptions
     */
    public function saveOptions(Options $options)
    {
        $containerName = self::ContainerPrefix . $options->getName();
        $container = new Container($containerName);
        $container->exchangeArray($options->getArrayCopy());
        return $this;
    }
}
