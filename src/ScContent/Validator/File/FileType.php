<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Validator\File;

use ScContent\Service\FileTypesCatalogInterface as CatalogInterface,
    //
    Zend\Validator\AbstractValidator,
    Zend\Validator\Exception\InvalidArgumentException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileType extends AbstractValidator
{
    /**
     * @var ScContent\Service\FileTypesCatalog
     */
    protected $catalog;

    /**
     * Options for this validator
     *
     * @var array
     */
    protected $patterns = array();

    /**#@+
     * @const string
     */
    const NotFound    = 'File not found';
    const NotDetected = 'File type not detected';
    const IllegalType = 'Illegal type';
    /**#@-*/

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::NotFound
            => 'File is not readable or does not exist.',

        self::NotDetected
            => 'The file type could not be detected from the file.',

        self::IllegalType
            => "File '%value%' has not been loaded, because these file types are not allowed.",
    );

    /**
     * @param null | array $options
     * @throws Zend\Validator\Exception\InvalidArgumentException
     */
    public function __construct($options = null, CatalogInterface $catalog = null)
    {
        $this->patterns = array(
            CatalogInterface::Safe
        );
        if (! is_null($catalog)) {
            $this->setFileTypesCatalog($catalog);
        } else if (is_array($options)
                    && array_key_exists('file_types_catalog', $options)
        ) {
            $this->setFileTypesCatalog($options['file_types_catalog']);
            unset($options['file_types_catalog']);
        } else if ($options instanceof CatalogInterface) {
            $this->setFileTypesCatalog($options);
            return;
        } else {
            throw new InvalidArgumentException(
                "Missing 'file_types_catalog' option."
            );
        }

        parent::__construct($options);
    }

    /**
     * @param ScContent\Service\FileTypesCatalogInterface $catalog
     * @return void
     */
    public function setFileTypesCatalog(CatalogInterface $catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * @return ScContent\Service\FileTypesCatalogInterface
     */
    public function getFileTypesCatalog()
    {
        return $this->catalog;
    }

    /**
     * @param array $patterns
     * @return void
     */
    public function setPatterns($patterns)
    {
        if (! is_array($patterns)) {
            $patterns = array($patterns);
        }
        $this->patterns = $patterns;
    }

    /**
     * @return array
     */
    public function getPatterns()
    {
        return $this->patterns;
    }

    /**
     * @param  string | array $value Real file name
     * @param  array $file  File data from \Zend\File\Transfer\Transfer optional
     * @return bool
     */
    public function isValid($value, $file = null)
    {
        if (is_string($value) && is_array($file)) {
            // Legacy Zend\Transfer API support
            $filename = $file['name'];
            $filetype = $file['type'];
            $file     = $file['tmp_name'];
        } elseif (is_array($value)) {
            if (! isset($value['tmp_name'])
                || !isset($value['name'])
                || !isset($value['type'])
            ) {
                $this->error(self::NotDetected);
                return false;
            }
            $file     = $value['tmp_name'];
            $filename = $value['name'];
            $filetype = $value['type'];
        } else {
            $file     = $value;
            $filename = basename($file);
            $filetype = null;
        }

        $this->setValue($filename);

        // Is file readable ?
        if (false === stream_resolve_include_path($file)) {
            $this->error(self::NotFound);
            return false;
        }

        $extension  = strtolower(substr($filename, strrpos($filename, '.') + 1));

        $mime = $filetype;
        if (empty($mime)) {
            $this->error(self::NotDetected);
            return false;
        }

        $spec = $extension . ':' . $mime;
        $patterns = $this->getPatterns();
        foreach ($patterns as $pattern) {
            if ($this->catalog->isAllowed($spec, $pattern)) {
                return true;
            }
        }
        $this->error(self::IllegalType);
        return false;
    }
}
