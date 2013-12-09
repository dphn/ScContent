<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileTypesCatalog implements FileTypesCatalogInterface
{
    /**
     * @var array
     */
    protected $catalogue = [

        // safe/multimedia/image/web/gdeditable
        'gif:image/gif'
            => 0xC818,

        'jpe:image/jpeg'
            => 0xC818,

        'jpe:image/pjpeg'
            => 0xC818,

        'jpg:image/jpeg'
            => 0xC818,

        'jpg:image/pjpeg'
            => 0xC818,

        'jpeg:image/jpeg'
            => 0xC818,

        'jpeg:image/pjpeg'
            => 0xC818,

        'png:image/png'
            => 0xC818,

        'png:image/x-png'
            => 0xC818,

        // safe/multimedia/image/web
        'bmp:image/bmp'
            => 0xC810,

        'bmp:image/x-ms-bmp'
            => 0xC810,

        // safe/multimedia/image
        'cmx:image/x-cmx'
            => 0xC800,

        'cmx:image/x-cmx'
            => 0xC800,

        'ico:image/x-ico'
            => 0xC800,

        'pbm:image/x-portable-bitmap'
            => 0xC800,

        'pcd:image/x-photo-cd'
            => 0xC800,

        'pct:image/x-pict'
            => 0xC800,

        'pgm:image/x-portable-graymap'
            => 0xC800,

        'pnm:image/x-portable-anymap'
            => 0xC800,

        'ppm:image/x-portable-pixmap'
            => 0xC800,

        'ppm:image/x-portable-pixmap'
            => 0xC800,

        'psd:application/photoshop'
            => 0xC800,

        'ras:image/x-cmu-raster'
            => 0xC800,

        'rgb:image/x-rgb'
            => 0xC800,

        'tif:image/tiff'
            => 0xC800,

        'tiff:image/tiff'
            => 0xC800,

        'xbm:image/x-xbitmap'
            => 0xC800,

        'xpm:image/x-xpixmap'
            => 0xC800,

        'xps:application/vnd.ms-xpsdocument'
            => 0xC800,

        // safe/multimedia/presentation
        'odp:application/vnd.oasis.opendocument.presentation'
            => 0xC080,

        'pps:application/vnd.ms-powerpoint'
            => 0xC080,

        'ppsm:application/vnd.ms-powerpoint.slideshow.macroEnabled.12'
            => 0xC080,

        'ppsm:application/vnd.ms-powerpoint.slideshow.macroEnabled.12'
            => 0xC080,

        'ppsx:application/vnd.openxmlformats-officedocument.presentationml.slideshow'
            => 0xC080,

        'ppt:application/vnd.ms-powerpoint'
            => 0xC080,

        'ppt:application/msword'
            => 0xC080,

        'pptm:application/vnd.ms-powerpoint.presentation.macroEnabled.12'
            => 0xC080,

        'pptx:application/vnd.openxmlformats-officedocument.presentationml.presentation'
            => 0xC080,

        // safe/multimedia/drawing
        'ai:application/illustrator'
            => 0xC020,

        'dwg:image/x-dwg'
            => 0xC020,

        'dxf:application/x-autocad'
            => 0xC020,

        'odg:application/vnd.oasis.opendocument.graphics'
            => 0xC020,

        // safe/multimedia/flash/web
        'swf:application/x-shockwave-flash'
            => 0xC100,

        // safe/multimedia/audio/web
        'ra:audio/vnd.rn-realaudio'
            => 0xC410,

        'rm:audio/x-pn-realaudio'
            => 0xC410,

        'ram:audio/x-pn-realaudio'
            => 0xC410,

        // safe/multimedia/audio
        'aac:audio/aac'
            => 0xC400,

        'aif:audio/aiff'
            => 0xC400,

        'aifc:audio/aiff'
            => 0xC400,

        'aiff:audio/aiff'
            => 0xC400,

        'au:audio/basic'
            => 0xC400,

        'au:audio/x-basic'
            => 0xC400,

        'au:audio/x-basic'
            => 0xC400,

        'm4a:audio/m4a'
            => 0xC400,

        'midi:audio/midi'
            => 0xC400,

        'mp3:audio/mpeg'
            => 0xC400,

        'rmi:audio/mid'
            => 0xC400,

        'rmid:audio/mid'
            => 0xC400,

        'sds:application/vnd.stardivision.chart'
            => 0xC400,

        'snd:audio/basic'
            => 0xC400,

        'wav:audio/wav'
            => 0xC400,

        'wav:audio/x-wav'
            => 0xC400,

        'wma:audio/x-ms-wma'
            => 0xC400,

        // safe/multimedia/video/web
        'asf:video/x-ms-asf'
            => 0xC210,

        'asr:video/x-ms-asf'
            => 0xC210,

        'asx:video/x-ms-asf'
            => 0xC210,

        'm4v:video/mp4'
            => 0xC210,

        'mov:video/quicktime'
            => 0xC210,

        'qt:video/quicktime'
            => 0xC210,

        // safe/multimedia/video
        'avi:video/avi'
            => 0xC200,

        'avi:video/x-msvideo'
            => 0xC200,

        'dv:video/x-dv'
            => 0xC200,

        'lsf:video/x-la-asf'
            => 0xC200,

        'lsf:video/x-la-asf'
            => 0xC200,

        'lsx:video/x-la-asf'
            => 0xC200,

        'lsx:video/x-la-asf'
            => 0xC200,

        'm1v:video/mpeg'
            => 0xC200,

        'm2v:video/mpeg'
            => 0xC200,

        'mp2:video/mpeg'
            => 0xC200,

        'mpe:video/mpeg'
            => 0xC200,

        'mpg:video/mpeg'
            => 0xC200,

        'mpeg:video/mpeg'
            => 0xC200,

        'mpv2:video/mpeg'
            => 0xC200,

        'rv:video/vnd.rn-realvideo'
            => 0xC200,

        'wmv:video/x-ms-wmv'
            => 0xC200,

        // safe/document/pdf
        'pdf:application/pdf'
            => 0xA040,

        // safe/document
        'doc:application/vnd.ms-word'
            => 0xA000,

        'doc:application/msword'
            => 0xA000,

        'docm:application/vnd.ms-word.document.macroEnabled.12'
            => 0xA000,

        'docx:application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            => 0xA000,

        'indd:application/x-indesign'
            => 0xA000,

        'odf:application/vnd.oasis.opendocument.formula'
            => 0xA000,

        'ods:application/vnd.oasis.opendocument.spreadsheet'
            => 0xA000,

        'odt:application/vnd.oasis.opendocument.text'
            => 0xA000,

        'one:application/msonenote'
            => 0xA000,

        'ps:application/postscript'
            => 0xA000,

        'pub:application/vnd.ms-publisher'
            => 0xA000,

        'pub:application/msword'
            => 0xA000,

        'rtf:application/rtf'
            => 0xA000,

        'rtx:application/rtf'
            => 0xA000,

        'sxw:application/vnd.sun.xml.writer'
            => 0xA000,

        'tex:application/x-tex'
            => 0xA000,

        'txt:text/plain'
            => 0xA000,

        'vsd:application/vnd.visio'
            => 0xA000,

        'wdb:application/vnd.ms-works'
            => 0xA000,

        'wks:application/vnd.ms-works'
            => 0xA000,

        'wpd:application/wordperfect'
            => 0xA000,

        'wps:application/vnd.ms-works'
            => 0xA000,

        'wri:application/x-mswrite'
            => 0xA000,

        'xls:application/vnd.ms-excel'
            => 0xA000,

        'xlsm:application/vnd.ms-excel.sheet.macroEnabled.12'
            => 0xA000,

        'xlsx:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            => 0xA000,

        'xps:application/vnd.ms-xpsdocument'
            => 0xA000,

        // safe/archive
        '7z:application/x-7z-compressed'
            => 0x9000,

        'bz2:application/x-bzip2'
            => 0x9000,

        'cab:application/vnd.ms-cab-compressed'
            => 0x9000,

        'cab:application/cab'
            => 0x9000,

        'cab:application/x-cab'
            => 0x9000,

        'gtar:application/tar'
            => 0x9000,

        'gz:application/gzip'
            => 0x9000,

        'gz:application/x-gzip'
            => 0x9000,

        'lzh:application/x-lha'
            => 0x9000,

        'rar:application/rar'
            => 0x9000,

        'rar:application/x-rar'
            => 0x9000,

        'tar:application/tar'
            => 0x9000,

        'z:application/x-compress'
            => 0x9000,

        'zip:application/zip'
            => 0x9000,
    ];

    /**
     * Returns true if and only if the file specification
     * is allowed for a given pattern.
     *
     * Usage:
     * <code>
     *     // for example, you have file extension and mime
     *     $extension = 'jpeg';
     *     $mime = 'image/jpeg';
     *
     *     $spec = $extension . ':' . $mime;
     *
     *     // you want to allow only editable images
     *     $pattern = Catalog::Image | Catalog::GdEditable;
     *
     *     $isAllowed = $catalog->isAllowed($spec, $pattern);
     * </code>
     *
     * How to allow editable images AND music?
     * <code>
     *     // for example, you have file extension and mime
     *     $extension = 'jpeg';
     *     $mime = 'image/jpeg';
     *
     *     $spec = $extension . ':' . $mime;
     *
     *     $patterns = array(
     *         Catalog::Image | Catalog::GdEditable,
     *         Catalog::Audio,
     *     );
     *     $isAllowed = false;
     *     foreach ($patterns as $pattern) {
     *         if ($catalog->isAllowed($spec, $pattern)) {
     *             $isAllowed = true;
     *         }
     *     }
     *     if ($isAllowed) {
     *         // your code here
     *     }
     * </code>
     *
     * The recommended pattern is Catalog::Safe
     *
     * @api
     *
     * @param string $spec
     * $param integer | array $pattern
     * @return boolean
     */
    public function isAllowed($spec, $pattern)
    {
        if (! array_key_exists($spec, $this->catalogue)) {
            return false;
        }
        $features = $this->catalogue[$spec];
        if (! is_array($pattern)) {
            $pattern = [$pattern];
        }
        $needle = 0;
        foreach ($pattern as $term) {
            $needle |= $term;
        }
        return $needle === ($features & $needle);
    }

    /**
     * Get feature (pattern) by specification
     *
     * @param string $spec
     * @return null | integer
     */
    public function getFeature($spec)
    {
        if (array_key_exists($spec, $this->catalogue)) {
            return $this->catalogue[$spec];
        }
    }

    /**
     * Get all known extensions by pattern
     *
     * Each new term does not extend the search results,
     * but makes them more specific.
     *
     * Usage:
     * <code>
     * $array = $catalogue->findExtensionByType(
     *     Catalog::Image | Catalog::GdEditable
     * );
     * </code>
     *
     * @api
     *
     * @param integer | array $pattern
     * @return array
     */
    public function findExtensionByType($pattern)
    {
        return $this->findByType($pattern, 0);
    }

    /**
     * Get all known MIME-types by pattern
     *
     * Each new term does not extend the search results,
     * but makes them more specific.
     *
     * Usage:
     * <code>
     * $array = $catalogue->findMimeByType(
     *     Catalog::Image | Catalog::GdEditable
     * );
     * </code>
     *
     * @api
     *
     * @param integer | array $pattern
     * @return array
     */
    public function findMimeByType($pattern)
    {
        return $this->findByType($pattern, 1);
    }

    /**
     * Each new term does not extend the search results,
     * but makes them more specific.
     *
     * @param integer | array $pattern
     * @param integer $part <code>0</code> for extension, <code>1</code> for mime
     * @return array
     */
    protected function findByType($pattern, $part)
    {
        if (! is_array($pattern)) {
            $pattern = [$pattern];
        }
        $needle = 0;
        foreach ($pattern as $term) {
            $needle |= $term;
        }
        $matches = [];
        foreach ($this->catalogue as $spec => $features) {
            if ($needle === ($features & $needle)) {
                $props = explode(':', $spec);
                $matches[] = $props[$part];
            }
        }
        return array_unique($matches);
    }
}
