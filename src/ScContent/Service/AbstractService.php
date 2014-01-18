<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service;

use ScContent\Exception\IoCException,
    //
    Zend\I18n\Translator\TranslatorAwareInterface,
    Zend\I18n\Translator\Translator;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractService extends EventProvider implements
    TranslatorAwareInterface
{
    /**
     * @var Zend\I18n\Translator\Translator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $textDomain = 'default';

    /**
     * @var boolean
     */
    protected $translatorEnabled = true;

    /**
     * @param Zend\I18n\Translator\Translator $translator
     * @param string $textDomain
     * @return Zend\I18n\Translator\TranslatorAwareInterface
     */
    public function setTranslator(
        Translator $translator = null,
        $textDomain = null
    ) {
        if (is_object($translator)) {
            $this->translator = $translator;
        }
        if (is_string($textDomain)) {
            $this->textDomain = $textDomain;
        }
        return $this;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return Zend\I18n\Translator\Translator
     */
    public function getTranslator()
    {
        if (! $this->translator instanceof Translator) {
            throw new IoCException('The translator was not set.');
        }
        return $this->translator;
    }

    /**
     * @return bool
     */
    public function hasTranslator()
    {
        return $this->translator instanceof Translator;
    }

    /**
     * @param boolean $enabled
     * @return Zend\I18n\Translator\TranslatorAwareInterface
     */
    public function setTranslatorEnabled($enabled = true)
    {
        $this->translatorEnabled = $enabled;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTranslatorEnabled()
    {
        return $this->translatorEnabled;
    }

    /**
     * @param string $textDomain
     * @return Zend\I18n\Translator\TranslatorAwareInterface
     */
    public function setTranslatorTextDomain($textDomain = 'default')
    {
        $this->textDomain = $textDomain;
        return $this;
    }

    /**
     * @return string
     */
    public function getTranslatorTextDomain()
    {
        return $this->textDomain;
    }
}
