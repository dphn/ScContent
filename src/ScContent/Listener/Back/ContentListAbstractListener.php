<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ScContent\Listener\Back;

use ScContent\Listener\AbstractControllerListener,
    ScContent\Service\Back\ContentListOptionsProvider as OptionsProvider,
    ScContent\Mapper\Back\ContentListOperationAbstract as Mapper,
    ScContent\Exception\IoCException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class ContentListAbstractListener extends AbstractControllerListener
{
    /**
     * @var \ScContent\Service\Back\ContentListOptionsProvider
     */
    protected $optionsProvider;

    /**
     * @var \ScContent\Mapper\Back\ContentListOperationAbstract
     */
    protected $mapper;

    /**
     * @param  \ScContent\Service\Back\ContentListOptionsProvider $provider
     * @return ContentListAbstractListener
     */
    public function setOptionsProvider(OptionsProvider $provider)
    {
        $this->optionsProvider = $provider;
        return $this;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Service\Back\ContentListOptionsProvider
     */
    public function getOptionsProvider()
    {
        if (! $this->optionsProvider instanceof OptionsProvider) {
            throw new IoCException(
                'The options provider was not set.'
            );
        }
        return $this->optionsProvider;
    }

    /**
     * @throws \ScContent\Exception\IoCException
     * @return \ScContent\Mapper\Back\ContentListOperationAbstract
     */
    public function getMapper()
    {
        if (! $this->mapper instanceof Mapper) {
            throw new IoCException(
                'The mapper was not set.'
            );
        }
        return $this->mapper;
    }
}
