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
    ScContent\Exception\InvalidArgumentException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class AutoloadService extends AbstractInstallationService
{
    /**
     * @var ScContent\Service\Dir
     */
    protected $dir;

    /**#@+
     * @const string
     */
    const AutoloadIsNotWritable = 'Autoload is not writable';
    const UnknownModule         = 'Unknown module';
    const MissingFile           = 'Missing file';
    const RemovingFailed        = 'Removing failed';
    const CopyFailed            = 'Copy failed';
    /**#@-*/

    /**
     * @var array
     */
    protected $errorMessages = [
        self::AutoloadIsNotWritable
            => 'Unable to copy files to the application autoload directory. Directory %s is not writable. Please, check the permissions, or copy the file %s manually.',

        self::UnknownModule
            => 'Unable to copy file to the application autoload directory. The module %s was not found.',

        self::MissingFile
            => 'Unable to copy file to the application autoload directory. The file %s was not found.',

        self::RemovingFailed
            => 'Failed to copy the file %s to  the configuration autoload directory. Failed to remove the following older versions of file: %s',

        self::CopyFailed
            => 'Failed to copy the file %s to  the configuration autoload directory.',
    ];

    /**
     * @param ScContent\Service\Dir $dir
     * @return void
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
        if (! isset($options['source_module'])) {
            throw new InvalidArgumentException(
                "Missing validation option 'source_module'."
            );
        }
        if (! isset($options['source_file'])) {
            throw new InvalidArgumentException(
                "Missing validation option 'source_file'."
            );
        }
        if (! isset($options['old_files_mask'])) {
            throw new InvalidArgumentException(
                "Missing validation option 'old_files_mask'."
            );
        }
        $dir = clone ($this->dir);

        $source = $dir->normalizePath($options['source_file']);
        $destination = basename($source, '.dist');

        if ($dir->appAutoload($destination, true)) {
            return true;
        }

        if (! is_writable($dir->appAutoload())) {
            $this->setValue(
                    $dir->getRelativeAppAutoloadDir(),
                    $options['source_module'] . DS . $source
                )
                ->error(self::AutoloadIsNotWritable);
            return false;
        }

        try {
            $dir->setModule($options['source_module']);
        } catch (InvalidArgumentException $e) {
            $this->setValue($options['source_module'])
                ->error(self::UnknownModule);
            return false;
        }

        if (! $dir->module($source, true)) {
            $this->setValue($options['source_module'] . DS . $source)
                ->error(self::MissingFile);
            return false;
        }

        $autoload = $dir->appAutoload();
        $mask = $dir->normalizePath($options['old_files_mask']);
        $failures = [];
        foreach (glob($autoload . DS . $mask) as $file) {
            if (! @unlink($file)) {
                $failures[] = str_replace($autoload, '', $file);
            }
        }

        if (! empty($failures)) {
            $failures = implode(', ', $failures);
            $this->setValue($options['source_file'], $failures)
                ->error(self::RemovingFailed);
            return false;
        }

        if (! copy($dir->module($source), $dir->appAutoload($destination))) {
            $this->setValue($options['source_module'] . DS . $source)
                ->error(self::CopyFailed);
            return false;
        }
        return true;
    }
}
