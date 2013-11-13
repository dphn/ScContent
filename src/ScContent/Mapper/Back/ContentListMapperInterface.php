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

use ScContent\Service\Back\ContentListOptionsProvider as OptionsProvider,
    //
    Zend\Db\Adapter\AdapterInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
interface ContentListMapperInterface
{
    /**
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     * @param ScContent\Service\Back\ContentListOptionsProvider $options
     */
    function __construct(AdapterInterface $adapter, OptionsProvider $options);

    /**
     * @param string $optionsIdentifier
     * @return ScContent\Entity\Back\ContentList
     */
    function getContent($optionsIdentifier);
}
