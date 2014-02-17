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

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
interface FileTypesCatalogInterface
{
    /**#@+
     * @const integer
     */
    const Safe         = 0x8000;
    const Multimedia   = 0x4000;
    const Document     = 0x2000;
    const Archive      = 0x1000;
    const Image        = 0x800;
    const Audio        = 0x400;
    const Video        = 0x200;
    const Flash        = 0x100;
    const Presentation = 0x80;
    const Pdf          = 0x40;
    const Drawing      = 0x20;
    const Web          = 0x10;
    const GdEditable   = 0x08;
    /**#@-*/

    /**
     * Returns true if and only if the file specification
     * is allowed for a given pattern.
     *
     * @param  string $spec
     * $param  integer|array $pattern
     * @return boolean
     */
    function isAllowed($spec, $pattern);

    /**
     * @param  string $spec
     * @return null|integer
     */
    function getFeature($spec);

    /**
     * Each new term does not extend the search results,
     * but makes them more specific.
     *
     * @param  integer|array $pattern
     * @return array
     */
    function findExtensionByType($pattern);

    /**
     * Each new term does not extend the search results,
     * but makes them more specific.
     *
     * @param  integer|array $pattern
     * @return array
     */
    function findMimeByType($pattern);
}
