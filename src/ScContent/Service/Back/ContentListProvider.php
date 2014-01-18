<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Back;

use ScContent\Service\AbstractService,
    ScContent\Service\Back\ContentListOptionsProvider,
    ScContent\Mapper\Back\ContentListMapperInterface,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\IoCException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListProvider extends AbstractService
{
    /**
     * @var ScContent\Service\ContentListOptionsProvider
     */
    protected $optionsProvider;

    /**
     * @var array
     */
    protected $lists = [
        'first'  => null,
        'second' => null,
    ];

    /**
     * @var array
     */
    protected $mappers = [
        'list'   => null,
        'search' => null,
    ];

    /**
     * @param ScContent\Service\ContentListOptionsProvider $provider
     * @return void
     */
    public function setOptionsProvider(ContentListOptionsProvider $provider)
    {
        $this->optionsProvider = $provider;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Service\ContentListOptionsProvider
     */
    public function getOptionsProvider()
    {
        if (! $this->optionsProvider instanceof ContentListOptionsProvider) {
            throw new IoCException(
                'The options provider was not set.'
            );
        }
        return $this->optionsProvider;
    }

    /**
     * @param ScContent\Mapper\Back\ContentListMapperInterface $mapper
     * @param string $type
     * @throws ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function setMapper(ContentListMapperInterface $mapper, $type)
    {
        if (! array_key_exists($type, $this->mappers)) {
            throw new InvalidArgumentException(sprintf(
                "Unknown mapper type '%s'.",
                $type
            ));
        }
        $this->mappers[$type] = $mapper;
    }

    /**
     * @param string $type
     * @throws ScContent\Exception\InvalidArgumentException
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Back\ContentListInterface
     */
    protected function getMapper($type)
    {
        if (! array_key_exists($type, $this->mappers)) {
            throw new InvalidArgumentException(sprintf(
                "Unknown mapper type '%s'.",
                $type
            ));
        }
        if (! $this->mappers[$type] instanceof ContentListMapperInterface) {
            throw new IoCException(sprintf(
                "The mapper '%s' was not set.",
                $type
            ));
        }
        return $this->mappers[$type];
    }

    /**
     * @param string $name
     * @throws ScContent\Exception\InvalidArgumentException
     * @return ScContent\Entity\ContentList
     */
    public function getList($name)
    {
        if (! array_key_exists($name, $this->lists)) {
            throw new InvalidArgumentException(sprintf(
                "Unknown list name '%s'.",
                $name
            ));
        }
        if (is_null($this->lists[$name])) {
            $optionsProvider = $this->getOptionsProvider();
            $options = $optionsProvider->getOptions($name);
            $mapper = $this->getMapper($options->getType());
            $this->lists[$name] = $mapper->getContent($name);
            $this->optionsProvider->save($name);
        }
        return $this->lists[$name];
    }
}
