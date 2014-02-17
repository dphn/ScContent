<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Options;

use Zend\Stdlib\AbstractOptions;
/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class InstallationOptions extends AbstractOptions
{
    /**
     * @var array
     */
    protected $installation = [];

    /**
     * @param  array $options
     * @return void
     */
    public function setInstallation($options)
    {
        $this->installation = $options;
    }

    /**
     * @return array
     */
    public function getInstallation()
    {
        return $this->installation;
    }
}
