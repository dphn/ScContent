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
    'factories' => [
        'scDateTime'
            => 'ScContent\Factory\View\Helper\DateTimeFactory',

        'scLocalization'
            => 'ScContent\Factory\View\Helper\LocalizationFactory',

        'scContentFormat'
            => 'ScContent\Factory\View\Helper\ContentFormatFactory',
    ],
];
