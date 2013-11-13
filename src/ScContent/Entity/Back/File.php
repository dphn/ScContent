<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity\Back;

use ScContent\Entity\AbstractContent;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class File extends AbstractContent
{
    /**
     * @var string
     */
    protected $type = 'file';

    /**
     * @var string
     */
    protected $status = 'published';
}
