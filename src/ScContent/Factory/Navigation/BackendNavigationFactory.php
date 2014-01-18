<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Factory\Navigation;

use Zend\Navigation\Service\AbstractNavigationFactory;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class BackendNavigationFactory extends AbstractNavigationFactory
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'sc-backend';
    }
}
