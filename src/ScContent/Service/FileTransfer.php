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

use ScContent\Service\FileTypesCatalogInterface as CatalogInterface,
    ScContent\Service\Stdlib,
    ScContent\Exception\RuntimeException,
    ScContent\Exception\DomainException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileTransfer implements FileTransferInterface
{
    /**
     * @var ScContent\Service\ThumbnailGeneratorInterface
     */
    protected $thumbnailGenerator;

    /**
     * @var ScContent\Service\FileTypesCatalog
     */
    protected $catalog;

    /**
     * @var ScContent\Service\Dir
     */
    protected $dir;

    /**
     * @param ScContent\Service\ThumbnailGeneratorInterface $thumbnailGenerator
     * @param ScContent\Service\FileTypesCatalogInterface $catalog
     * @param ScContent\Service\Dir $dir
     * @return void
     */
    public function __construct(
        ThumbnailGeneratorInterface $thumbnailGenerator,
        CatalogInterface $catalog,
        Dir $dir
    ) {
        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->catalog = $catalog;
        $this->dir = $dir;
    }

    /**
     * @param array $data
     * @throws ScContent\Exception\RuntimeException
     * @return void
     */
    public function receive($files)
    {
        $dir = $this->dir;
        if (! $dir->appUploads('', true)) {
            throw new RuntimeException(
                'Uploads directory does not exist or is unavailable.'
            );
        }
        $info = array();
        $files = $files['file'];
        if (isset($files['name'])) {
            $files = array(
                $files
            );
        }
        foreach ($files as $file) {
            $data = array();
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
                $rand = Stdlib::randomKey(6);
                $name = $title . ' (' . $rand . ')';
                $fileName = $name . '.' . $extension;
            } while ($dir->appUploads($fileName, true));

            $newFile = $dir->appUploads($fileName);

            $data['name'] = $name;

            if (! @copy($file['tmp_name'], $newFile)) {
                continue;
            }
            @chmod($newFile, CHMOD_FILE);
            @unlink($file['tmp_name']);

            if ($this->catalog->getFeature($spec) & CatalogInterface::GdEditable) {
                try {
                    $this->thumbnailGenerator->generate($newFile);
                } catch (DomainException $e) {
                    @unlink($newFile);
                    continue;
                } catch (RuntimeException $e) {
                    @unlink($newFile);
                    continue;
                }
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
        $dir = $this->dir;
        foreach ($data as $file) {
            list ($extension, $mime) = explode(':', $file['spec']);
            $fileName = $file['name'] . '.' . $extension;
            $fileThumbnail = $file['name'] . '.thumbnail.' . $extension;
            if ($dir->appUploads($fileName, true)) {
                @unlink($dir->appUploads($fileName));
            }
            if ($dir->appUploads($fileThumbnail, true)) {
                @unlink($dir->appUploads($fileThumbnail));
            }
        }
    }
}
