<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Installation;

use ScContent\Service\AbstractIntelligentService,
    ScContent\Exception\InvalidArgumentException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractInstallationService extends AbstractIntelligentService
{
    /**
     * @var string
     */
    protected $valueFormat = '<code>%s</code>';

    /**
     * @param mixed $options optional
     * @return boolean
     */
    abstract public function process($options);
}
