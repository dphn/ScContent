<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Controller;

use ScContent\Entity\WidgetInterface,
    ScContent\Exception\IoCException,
    //
    Zend\Mvc\Controller\AbstractActionController;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractWidget extends AbstractActionController
{
    /**
     * @var ScContent\Entity\WidgetInterface
     */
    protected $item;

    public function setItem(WidgetInterface $item)
    {
        $this->item = $item;
    }

    /**
     * @return ScContent\Entity\WidgetInterface
     */
    public function getItem()
    {
        if (! $this->item instanceof WidgetInterface) {
            throw new IoCException(
                'Item data was not set.'
            );
        }
        return $this->item;
    }

    /**
     * @return mixed
     */
    abstract function frontAction();

    /**
     * @return mixed
     */
    abstract function backAction();
}
