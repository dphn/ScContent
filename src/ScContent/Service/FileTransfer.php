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

use ScContent\Service\FileTypesCatalogInterface as CatalogInterface,
    ScContent\Service\Stdlib,
    ScContent\Exception\IoCException,
    ScContent\Exception\DebugException,
    ScContent\Exception\RuntimeException,
    //
    Zend\Validator\Db\NoRecordExists,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileTransfer extends AbstractService implements FileTransferInterface
{
    /**
     * @var Zend\Validator\Db\NoRecordExists
     */
    protected $validator;

    /**
     * @var FileTypesCatalog
     */
    protected $catalog;

    /**
     * @var Dir
     */
    protected $dir;

    /**
     * @param Zend\Validator\Db\NoRecordExists $validator
     * @return void
     */
    public function setValidator(NoRecordExists $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return Zend\Validator\Db\NoRecordExists
     */
    public function getValidator()
    {
        if (! $this->validator instanceof NoRecordExists) {
            throw new IoCException(
                'The validator was not set.'
            );
        }
        return $this->validator;
    }

    /**
     * @param FileTypesCatalogInterface $catalog
     * @return void
     */
    public function setCatalog(CatalogInterface $catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return FileTypesCatalogInterface
     */
    public function getCatalog()
    {
        if (! $this->catalog instanceof CatalogInterface) {
            throw new IoCException(
                'The file types catalog was not set.'
            );
        }
        return $this->catalog;
    }

    /**
     * @param Dir $dir
     * @return void
     */
    public function setDir(Dir $dir)
    {
        $this->dir = $dir;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return Dir
     */
    public function getDir()
    {
        if (! $this->dir instanceof Dir) {
            throw new IoCException(
                'The directory service was not set.'
            );
        }
        return $this->dir;
    }

    /**
     * @param array $data
     * @throws ScContent\Exception\RuntimeException
     * @return void
     */
    public function receive($files)
    {
        $dir = $this->getDir();
        $catalog = $this->getCatalog();
        $events = $this->getEventManager();
        $translator = $this->getTranslator();
        $validator = $this->getValidator();

        $uploads = $dir->appUploads('', true);
        if (! $uploads) {
            throw new RuntimeException(
                'Directory for uploading files does not exist or is not available.'
            );
        }
        if (! is_writable($uploads)) {
            throw new RuntimeException(
                'Directory for uploading files is not writable.'
            );
        }
        $info = [];
        $files = $files['file'];
        if (isset($files['name'])) {
            $files = [$files];
        }
        foreach ($files as $file) {
            $data = [];
            $mime = $file['type'];
            $filename = $file['name'];
            $extension = strtolower(
                substr($filename, strrpos($filename, '.') + 1)
            );

            // spec
            $spec = $extension . ':' . $mime;
            $data['spec'] = $spec;

            // title
            $title = substr($filename, 0, strpos($filename, '.'));
            $data['title'] = $title;

            // name
            do {
                $name = $title . '-' . Stdlib::randomKey(6);
                $fileName = $name . '.' . $extension;
            } while ($dir->appUploads($fileName, true) && ! $validator->isValid($name));

            $newFile = $dir->appUploads($fileName);

            $data['name'] = $name;

            if (! @copy($file['tmp_name'], $newFile)) {
                continue;
            }
            @chmod($newFile, CHMOD_FILE);
            @unlink($file['tmp_name']);

            try {
                $events->trigger(
                    __FUNCTION__,
                    null,
                    [
                        'file' => $newFile,
                        'spec' => $spec,
                    ]
                );
            } catch (Exception $e) {
                @unlink($newFile);
                if (DEBUG_MODE) {
                    throw new DebugException(
                        $translator->translate('Error: ') . $e->getMessage(),
                        $e->getCode(),
                        $e
                    );
                }
                continue;
            }
            $info[] = $data;
        }
        return $info;
    }

    /**
     * @param array $data
     * @return void
     */
    public function rollBack($data)
    {
        $events = $this->getEventManager();
        $dir = $this->getDir();

        foreach ($data as $file) {
            list ($extension, $mime) = explode(':', $file['spec']);
            $fileName = $file['name'] . '.' . $extension;
            if ($dir->appUploads($fileName, true)) {
                @unlink($dir->appUploads($fileName));
            }
            $events->trigger(
                __FUNCTION__,
                null,
                [
                    'file' => $fileName,
                    'spec' => $spec,
                ]
            );
        }
    }
}
