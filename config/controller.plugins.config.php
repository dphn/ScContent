<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return [
    'invokables' => [
        'widgetAvailability'
            => 'ScContent\Controller\Plugin\WidgetAvailability',
    ],
    'factories' => [
        'scTranslate'
            => 'ScContent\Factory\Controller\Plugin\TranslatorFactory',
    ],
];
