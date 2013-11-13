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

use ScContent\Exception\RuntimeException,
    ScContent\Exception\DomainException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ThumbnailGenerator implements ThumbnailGeneratorInterface
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
     * @param string $path Path to source file.
     * @throws ScContent\Exception\DomainException
     * @throws ScContent\Exception\RuntimeException
     * @return void
     */
    public function generate($path)
    {
        extract(pathinfo($path));
        if (! $imageInfo = getimagesize($path)) {
            throw new DomainException('The selected file is not an image.');
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
                throw new DomainException('Unable to edit this image type.');
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
            throw new RuntimeException('Thumbnail creation failed.');
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
}
