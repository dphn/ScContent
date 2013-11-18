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

use ScContent\Service\AbstractIntelligentService,
    ScContent\Entity\Installation\DatabaseConfig,
    ScContent\Mapper\Installation\ConfigMapper,
    ScContent\Service\Dir,
    ScContent\Exception\InvalidArgumentException,
    ScContent\Exception\DebugException,
    ScContent\Exception\IoCException,
    //
    Exception;

/**
 * Creates the internal configuration of ScContent module.
 *
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ConfigService extends AbstractIntelligentService
{
    /**
     * @var ScContent\Service\Dir
     */
    protected $dir;

    /**
     * @var ScContent\Mapper\Installation\ConfigMapper
     */
    protected $mapper;

    /**
     * @var ScContent\Entity\Installation\DatabaseConfig
     */
    protected $config;

    /**
     * @var string
     */
    protected $valueFormat = '<code>%s</code>';

    /**#@+
     * @const string
     */
    const AutoloadIsNotWritable  = 'Autoload is not writable';
    const MissingSourceFile      = 'Missing source file';
    const RemovingFailed         = 'Removing failed';
    const FailedToSave           = 'Failed to save';
    /**#@-*/

    /**
     * @var array
     */
    protected $errorMessages = array(
        self::AutoloadIsNotWritable
            => 'Unable to create the configuration file %s. The application autoload directory is not writable.',

        self::MissingSourceFile
            => 'Unable to create the ScContent configuration. Missing source file %s.',

        self::RemovingFailed
            => 'Unable to create the ScContent configuration. Failed to remove the following older versions of file: %s',

        self::FailedToSave
            => 'Unable to save ScContent configuration.',
    );

    /**
     * @param ScContent\Service\Dir $dir
     * @return void
     */
    public function setDir(Dir $dir)
    {
        $this->dir = $dir;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Service\Dir
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
     * @param ScContent\Mapper\Installation\ConfigMapper
     * @return void
     */
    public function setMapper(ConfigMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @return ScContent\Mapper\Installation\ConfigMapper
     */
    public function getMapper()
    {
        if (! $this->mapper instanceof ConfigMapper) {
            $this->mapper = new ConfigMapper();
        }
        return $this->mapper;
    }

    /**
     * @return ScContent\Entity\Installation\DatabaseConfig
     */
    public function getConfig()
    {
        if (! $this->config instanceof DatabaseConfig) {
            $this->config = new DatabaseConfig();
        }
        return $this->config;
    }

    /**
     * @param ScContent\Entity\Installation\DatabaseConfig $config
     * @param array $options
     * @throws ScContent\Exception\InvalidArgumentException
     * @throws ScContent\Exception\DebugException
     * @return boolean
     */
    public function saveConfig(DatabaseConfig $config, $options)
    {
        $dir = $this->getDir();
        $mapper = $this->getMapper();
        $translator = $this->getTranslator();
        if (! is_array($options)) {
            throw new InvalidArgumentException(
                $translator->translate(
                    "The options must be an array of values."
                )
            );
        }
        if (! isset($options['old_files_mask'])) {
            throw new InvalidArgumentException(
                $translator->translate(
                    "Missing 'old_files_mask' option."
                )
            );
        }
        if (! isset($options['source_file'])) {
            throw new InvalidArgumentException(
                $translator->translate(
                    "Missing 'source_file' option."
                )
            );
        }

        $source = $dir->normalizePath($options['source_file']);
        $destination = basename($source, '.dist');
        $autoload = $dir->appAutoload();

        if (! is_writable($autoload)) {
            $this->setValue($destination)->error(self::AutoloadIsNotWritable);
            return false;
        }

        if (! $dir->module($source, true)) {
            $this->setValue($source)->error(self::MissingSourceFile);
            return false;
        }

        $mask = $dir->normalizePath($options['old_files_mask']);
        $failures = array();
        foreach (glob($autoload . DS . $mask) as $file) {
            if (! @unlink($file)) {
                $failures[] = str_replace($autoload, '', $file);
            }
        }
        if (! empty($failures)) {
            $failures = implode(', ', $failures);
            $this->setValue($failures)->error(self::RemovingFailed);
            return false;
        }
        try {
            $mapper->save(
                $config,
                $dir->module($source),
                $dir->appAutoload($destination)
            );
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                throw new DebugException(
                    'Error: ' . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
            $this->serValue()->error(self::FailedToSave);
            return false;
        }
        return true;
    }
}
