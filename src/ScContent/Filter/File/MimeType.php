<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Filter\File;

use Zend\Filter\AbstractFilter,
    Zend\Stdlib\ErrorHandler,
    Zend\Filter\Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 *
 * The code for this class is inspired from the standard ZF2
 * class "Zend\Vaildator\File\MimeType".
 */
class MimeType extends AbstractFilter
{
    /**
     * Finfo object to use
     *
     * @var resource
     */
    protected $finfo;

    /**
     * If no environment variable 'MAGIC' is set, try and autodiscover
     * it based on common locations
     *
     * @var array
     */
    protected $magicFiles = array(
        '/usr/share/misc/magic',
        '/usr/share/misc/magic.mime',
        '/usr/share/misc/magic.mgc',
        '/usr/share/mime/magic',
        '/usr/share/mime/magic.mime',
        '/usr/share/mime/magic.mgc',
        '/usr/share/file/magic',
        '/usr/share/file/magic.mime',
        '/usr/share/file/magic.mgc',
    );

    /**
     * Options for this filter
     *
     * @var array
     */
    protected $options = array(
        'disableMagicFile'  => false,  // Disable usage of magicfile
        'magicFile'         => null,   // Magicfile to use
    );

    /**
     * Returns the actual set magicfile
     *
     * @return string
     */
    public function getMagicFile()
    {
        if (null === $this->options['magicFile']) {
            $magic = getenv('magic');
            if (! empty($magic)) {
                $this->setMagicFile($magic);
                if ($this->options['magicFile'] === null) {
                    $this->options['magicFile'] = false;
                }
                return $this->options['magicFile'];
            }

            ErrorHandler::start();
            $safeMode = ini_get('safe_mode');
            ErrorHandler::stop();
            if (! ($safeMode == 'On' || $safeMode === 1)) {
                foreach ($this->magicFiles as $file) {
                    // suppressing errors which are thrown due to openbase_dir restrictions
                    try {
                        $this->setMagicFile($file);
                        if ($this->options['magicFile'] !== null) {
                            break;
                        }
                    } catch (\Exception $e) {
                        // Intentionally, catch and fall through
                    }
                }
            }

            if ($this->options['magicFile'] === null) {
                $this->options['magicFile'] = false;
            }
        }

        return $this->options['magicFile'];
    }

    /**
     * Sets the magicfile to use
     * if null, the MAGIC constant from php is used
     * if the MAGIC file is erroneous, no file will be set
     * if false, the default MAGIC file from PHP will be used
     *
     * @param  string $file
     * @throws Zend\Filter\Exception\RuntimeException When finfo can not read the magicfile
     * @throws Zend\Filter\Exception\InvalidArgumentException
     * @return MimeType Provides fluid interface
     */
    public function setMagicFile($file)
    {
        if ($file === false) {
            $this->options['magicFile'] = false;
        } elseif (empty($file)) {
            $this->options['magicFile'] = null;
        } elseif (! (class_exists('finfo', false))) {
            $this->options['magicFile'] = null;
            throw new Exception\RuntimeException(
                'Magicfile can not be set; there is no finfo extension installed');
        } elseif (! is_file($file) || ! is_readable($file)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The given magicfile ("%s") could not be read',
                $file
            ));
        } else {
            ErrorHandler::start(E_NOTICE|E_WARNING);
            $this->finfo = finfo_open(FILEINFO_MIME_TYPE, $file);
            $error       = ErrorHandler::stop();
            if (empty($this->finfo)) {
                $this->finfo = null;
                throw new Exception\InvalidArgumentException(sprintf(
                    'The given magicfile ("%s") could not be used by ext/finfo',
                    $file
                ), 0, $error);
            }
            $this->options['magicFile'] = $file;
        }

        return $this;
    }

    /**
     * Disables usage of MagicFile
     *
     * @param $disable boolean False disables usage of magic file
     * @return MimeType Provides fluid interface
     */
    public function disableMagicFile($disable)
    {
        $this->options['disableMagicFile'] = (bool) $disable;
        return $this;
    }

    /**
     * Is usage of MagicFile disabled?
     *
     * @return bool
     */
    public function isMagicFileDisabled()
    {
        return $this->options['disableMagicFile'];
    }

    /**
     * @param string | array $value
     * @param string $default
     * @throws Zend\Filter\Exception\InvalidArgumentException
     * @return string
     */
    public function filter($value, $default = null)
    {
        if (is_string($value)) {
            $file = $value;
        } elseif (is_array($value) && isset($value['tmp_name'])) {
            $file = $value['tmp_name'];
            if (isset($value['type'])) {
                $default = $value['type'];
            }
        } else {
            throw new Exception\InvalidArgumentException(
                'Value array must be string or in $_FILES format'
            );
        }
        if (false === stream_resolve_include_path($file)) {
            if (is_array($value) && isset($value['type'])) {
                $value['type'] = $default;
                return $value;
            }
            return $default;
        }
        if (! class_exists('finfo', false)) {
            if (is_array($value) && isset($value['type'])) {
                $value['type'] = $default;
                return $value;
            }
            return $default;
        }

        $mimefile = $this->getMagicFile();

        if (! $this->isMagicFileDisabled()
             && (! empty($mimefile) && empty($this->finfo))
        ) {
            ErrorHandler::start(E_NOTICE|E_WARNING);
            $this->finfo = finfo_open(FILEINFO_MIME_TYPE, $mimefile);
            ErrorHandler::stop();
        }
        if (empty($this->finfo)) {
            ErrorHandler::start(E_NOTICE|E_WARNING);
            $this->finfo = finfo_open(FILEINFO_MIME_TYPE);
            ErrorHandler::stop();
        }

        if (empty($this->finfo)) {
            if (is_array($value) && isset($value['type'])) {
                $value['type'] = $default;
                return $value;
            }
            return $default;
        }

        $mime = finfo_file($this->finfo, $file);
        if (empty($mime)) {
            if (is_array($value) && isset($value['type'])) {
                $value['type'] = $default;
                return $value;
            }
            return $default;
        }

        if (is_array($value) && isset($value['type'])) {
            $value['type'] = $mime;
            return $value;
        }
        return $mime;
    }
}
