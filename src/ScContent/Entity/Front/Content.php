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

use ScContent\Entity\AbstractContent,
    ScContent\View\Helper\FormatProviderInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Content extends AbstractContent implements FormatProviderInterface
{
    /**
     * @param  string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
