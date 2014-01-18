<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Installation;

use ScContent\Service\Dir;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class UploadsService extends AbstractInstallationService
{
    /**
     * @var ScContent\Service\Dir
     */
    protected $dir;

    /**
     * @const string
     */
    const FailedCreateDirectory = 'Failed create directory';

    /**
     * @var array
     */
    protected $errorMessages = [
        self::FailedCreateDirectory
            => 'Failed to create uploads directory %s. Please, check permissions or create this directory manually.',
    ];

    /**
     * Constructor
     *
     * @param ScContent\Service\Dir $dir
     * @return void
     */
    public function __construct(Dir $dir)
    {
        $this->dir = $dir;
    }

    /**
     * @param null $options Not uses
     * @return boolean
     */
    public function process($options = null)
    {
        $dir = $this->dir;

        if ($dir->appUploads('', true)) {
            return true;
        }
        if (! @mkdir($dir->appUploads(), CHMOD_DIR, true)) {
            $this->setValue($dir->getRelativeAppUploadsDir());
            $this->error(self::FailedCreateDirectory);
            return false;
        }
        @chmod($dir->appUploads(), CHMOD_DIR);
        $html = '<html><body style="background:#ffffff"></body></html>';
        @file_put_contents($dir->appUploads('index.html'), $html);
        @chmod($dir->appUploads('index.html'), CHMOD_FILE);
        return true;
    }
}
