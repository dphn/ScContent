<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\View\Helper;

use ScContent\Service\Localization as L10n,
    //
    Zend\View\Helper\AbstractHelper;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class Localization extends AbstractHelper
{
    /**
     * @var \ScContent\Service\Loalization
     */
    protected $l10n;

    /**
     * @param  \ScContent\Service\Loalization $l10n
     * @return void
     */
    public function __construct(L10n $l10n)
    {
        $this->l10n = $l10n;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->l10n->getLocale();
    }

    /**
     * @return string
     */
    public function getPrimaryLanguage()
    {
        return $this->l10n->getPrimaryLanguage();
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->l10n->getRegion();
    }
}
