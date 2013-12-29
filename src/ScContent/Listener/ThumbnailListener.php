<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener;

use ScContent\Service\FileTypesCatalogInterface as CatalogInterface,
    //
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\RuntimeException,
    ScContent\Exception\IoCException,
    //
    Zend\EventManager\EventInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ThumbnailListener extends AbstractListener
{
    /**
     * @const integer
     */
    const MaxSize = 150;

    /**
     * @const string
     */
    const Postfix = 'thumbnail';

    /**
     * @var ScContent\Service\FileTypesCatalogInterface
     */
    protected $catalog;

    /**
     * @param ScContent\Service\FileTypesCatalogInterface $catalog
     * @return void
     */
    public function setCatalog(CatalogInterface $catalog)
    {
        $this->catalog = $catalog;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Service\FileTypesCatalogInterface
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
     * @param Zend\EventManager\EventInterface $event
     * @throws ScContent\Exception\InvalidArgumentException
     * @throws ScContent\Exception\RuntimeException
     * @return void
     */
    public function generate(EventInterface $event)
    {
        $translator = $this->getTranslator();
        $catalog = $this->getCatalog();

        $path = $event->getParam('file');
        if (empty($path)) {
            throw new InvalidArgumentException(sprintf(
                $translator->translate("Missing event param '%s'."),
                'file'
            ));
        }

        $spec = $event->getParam('spec');
        if (empty($spec)) {
            throw new InvalidArgumentException(sprintf(
                $translator->translate("Missing event param '%s'."),
                'spec'
            ));
        }

        if (! ($catalog->getFeature($spec) & CatalogInterface::GdEditable)) {
            return;
        }

        extract(pathinfo($path));
        if (! $imageInfo = getimagesize($path)) {
            throw new RuntimeException(sprintf(
                $translator->translate("The file '%s' is not an image."),
                $path
            ));
        }

        $maxSourceSide = max($imageInfo[0], $imageInfo[1]);
        if ($maxSourceSide < self::MaxSize) {
            // The image is too small.
            return;
        }

        switch ($imageInfo['mime']) {
            case 'image/png':
                $sourceImage = imagecreatefrompng($path);
                break;
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($path);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($path);
                break;
            default:
                return;
        }
        $destinationPath = $dirname . DS . $filename . '.' . self::Postfix . '.' . $extension;
        $destinationImage = imagecreatetruecolor(self::MaxSize, self::MaxSize);
        $color = imagecolorallocate($destinationImage, 255, 255, 255);
        imagefilledrectangle($destinationImage,
            0, 0,
            imagesx($destinationImage), imagesy($destinationImage),
            $color
        );
        if ($imageInfo[0] / self::MaxSize < $imageInfo[1] / self::MaxSize) {
            $sourceCropX = 0;
            $sourceX = imagesx($sourceImage);
            $sourceY = $sourceX;
            $sourceCropY = round((imagesy($sourceImage) - $sourceY) / 2);
        } else {
            $sourceCropY = 0;
            $sourceY = imagesy($sourceImage);
            $sourceX = $sourceY;
            $sourceCropX = round((imagesx($sourceImage) - $sourceX) / 2);
        }
        $res = imagecopyresampled(
            $destinationImage, $sourceImage,
            0, 0,
            $sourceCropX, $sourceCropY,
            imagesx($destinationImage), imagesy($destinationImage),
            $sourceX, $sourceY
        );
        if (! $res) {
            imagedestroy($sourceImage);
            imagedestroy($destinationImage);
            throw new RuntimeException(
                $translator->translate('Failed to create thumbnail.')
            );
        }
        switch ($imageInfo['mime']) {
            case 'image/png':
                imagepng($destinationImage, $destinationPath);
                break;
            case 'image/jpeg':
                imagejpeg($destinationImage, $destinationPath);
                break;
            case 'image/gif':
                imagegif($destinationImage, $destinationPath);
                break;
        }
        imagedestroy($sourceImage);
        imagedestroy($destinationImage);
        @chmod($destinationPath, CHMOD_FILE);
    }

    /**
     * @param Zend\EventManager\EventInterface $event
     * @throws ScContent\Exception\InvalidArgumentException
     * @return void
     */
    public function remove(EventInterface $event)
    {
        $catalog = $this->getCatalog();
        $translator = $this->getTranslator();

        $path = $event->getParam('file');
        if (empty($path)) {
            throw new InvalidArgumentException(sprintf(
                $translator->translate("Missing event param '%s'."),
                'file'
            ));
        }

        $spec = $event->getParam('spec');
        if (empty($spec)) {
            throw new InvalidArgumentException(sprintf(
                $translator->translate("Missing event param '%s'."),
                'spec'
            ));
        }

        if (! ($catalog->getFeature($spec) & CatalogInterface::GdEditable)) {
            return;
        }

        $dirname = dirname($path);
        $basename = basename($path);

        $extension = substr($basename, strrpos($basename, '.') + 1);
        $filename = substr($basename, 0, strrpos($basename, '.') - 1);

        $thumbnail = $dirname . DS . $filename . '.' . self::Postfix . '.' . $extension;
        @unlink($thumbnail);
    }
}
