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

use Zend\View\Helper\AbstractHelper,
    //
    Locale;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class LanguageDirection extends AbstractHelper
{
    /**
     * @const string
     */
    const Ltr = 'ltr';

    /**
     * @const string
     */
    const Rtl = 'rtl';

    /**
     * ISO 639-1 Codes
     *
     * http://en.wikipedia.org/wiki/Right-to-left
     * http://www.loc.gov/standards/iso639-2/php/code_list.php
     * http://www.iana.org/assignments/language-subtag-registry/language-subtag-registry
     *
     * @var string[]
     */
    protected $rtlLanguages = [
        //                        | ISO 639-1 | ISO 639-2
        // Arabic                 | ar        | ara
        // Egyptian Spoken Arabic | ar        | arz
        'ar',
        // Aramaic                | missing   | arc
        // Southern Balochi       | missing   | bal
        // Bakthiari              | missing   | missing
        // Sorani                 | missing   | missing
        // Dhivehi                | dv        | div
        'dv',
        // Persian                | fa        | per (B) fas (T)
        'fa',
        // Gilaki                 | missing   | missing
        // Hebrew                 | he        | heb
        'he',
        // Kurdish                | ku        | kur
        'ku',
        // Mazanderani            | missing   | missing
        // Western Punjabi        | missing   | missing
        // Pashto                 | ps        | pus
        'ps',
        // Sindhi                 | sd        | snd
        'sd',
        // Uyghur                 | ug        | uig
        'ug',
        // Urdu                   | ur        | urd
        'ur',
        // Yiddish                | yi        | yid
        'yi',
    ];
    /**
     * @param string $locale
     */
    public function __invoke($locale = null)
    {
        if (is_null($locale)) {
            $locale = Locale::getDefault();
        }
        $primaryLanguage = Locale::getPrimaryLanguage($locale);
        if (in_array($primaryLanguage, $this->rtlLanguages)) {
            return self::Rtl;
        }
        return self::Ltr;
    }
}
