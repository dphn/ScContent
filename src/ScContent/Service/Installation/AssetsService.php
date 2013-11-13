<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Installation;

use ScContent\Service\Dir,
    ScContent\Exception\InvalidArgumentException,
    //
    ZipArchive;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AssetsService extends AbstractInstallationService
{
    /**
     * @var ScContent\Service\Dir
     */
    protected $dir;

    /**#@+
     * @const string
     */
    const DestinationIsNotWritable = 'Destination is not writable';
    const UnknownModule            = 'Unknown module';
    const MissingSourceArchive     = 'Missing source archive';
    const UnableToOpenArchive      = 'Unable to open archive';
    const RecursiveError           = 'Recursive error';
    /**#@-*/

    /**
     * @var array
     */
    protected $errorMessages = array(
        self::DestinationIsNotWritable
            => 'Unable to extract assets. The target directory %s is not writable.',

        self::UnknownModule
            => 'Failed to install the assets for an unknown module %s.',

        self::MissingSourceArchive
            => 'Unable to install assets. Archive of %s is not found.',

        self::UnableToOpenArchive
            => 'Unable to open archive %s. The archive is corrupt.',

        self::RecursiveError
            => 'A configuration error. The archive %s was extracted, but the directory, specified as the target does not exist.',
    );

    /**
     * @param ScContent\Service\Dir $dir
     */
    public function __construct(Dir $dir)
    {
        $this->dir = $dir;
    }

    /**
     * @param array $options
     * @throws ScContent\Exception\InvalidArgumentException
     * @return boolean
     */
    public function process($options)
    {
        if (! isset($options['validate_if_exists'])) {
            throw new InvalidArgumentException(
                "Missing option 'validate_if_exists'."
            );
        }
        if (! isset($options['source'])) {
            throw new InvalidArgumentException(
                "Missing 'source' options."
            );
        }
        if (! isset($options['source']['source_module'])) {
            throw new InvalidArgumentException(
                "Not specified 'source_module' in 'source' options."
            );
        }
        if (! isset($options['source']['source_zip'])) {
            throw new InvalidArgumentException(
                "Not specified 'source_zip' in 'source' options."
            );
        }

        $dir = clone ($this->dir);

        // if directory is already exists
        if ($dir->appPublic($options['validate_if_exists'], true)) {
            return true;
        }

        @set_time_limit(30);
        try {
            $dir->setModule($options['source']['source_module']);
        } catch (InvalidArgumentException $e) {
            $this->setValue($options['source']['source_module']);
            $this->error(self::UnknownModule);
            return false;
        }

        if (! is_writable($dir->appPublic())) {
            $this->setValue($dir->getRelativeAppPublicDir());
            $this->error(self::DestinationIsNotWritable);
            return false;
        }

        $source = $dir->normalizePath($options['source']['source_zip']);
        $archiveDisplayName = $options['source']['source_module'] . DS . $source;
        if (! $dir->module($source, true)) {
            $this->setValue($archiveDisplayName);
            $this->error(self::MissingSourceArchive);
            return false;
        }

        $zip = new ZipArchive();
        $result = $zip->open($dir->module($source));
        if (! $result) {
            $this->setValue($archiveDisplayName);
            $this->error(self::UnableToOpenArchive);
            return false;
        }

        $zip->extractTo($dir->appPublic());
        $zip->close();

        if (! $dir->appPublic($options['validate_if_exists'], true)) {
            $this->setValue($archiveDisplayName);
            $this->error(self::RecursiveError);
            return false;
        }

        return true;
    }
}
