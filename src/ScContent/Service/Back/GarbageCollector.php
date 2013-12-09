<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Back;

use ScContent\Service\AbstractService,
    ScContent\Mapper\Back\GarbageMapper,
    ScContent\Service\Dir,
    //
    ScContent\Exception\RuntimeException,
    ScContent\Exception\IoCException,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class GarbageCollector extends AbstractService
{
    /**
     * @const integer
     */
    const FilesPerOperation = 100;

    /**
     * @var ScContent\Service\Dir
     */
    protected $dir;

    /**
     * @var ScContent\Mapper\Back\GarbageMapper
     */
    protected $garbageMapper;

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
     * @param ScContent\Mapper\Back\GarbageMapper $mapper
     * @return void
     */
    public function setGarbageMapper(GarbageMapper $mapper)
    {
        $this->garbageMapper = $mapper;
    }

    /**
     * @throws ScContent\Exception\IoCException
     * @return ScContent\Mapper\Back\GarbageMapper
     */
    public function getGarbageMapper()
    {
        if (! $this->garbageMapper instanceof GarbageMapper) {
            throw new IoCException(
                'The garbage mapper was not set.'
            );
        }
        return $this->garbageMapper;
    }

    /**
     * @throws ScContent\Exception\RuntimeException
     * @return boolean
     */
    public function collect()
    {
        $translator = $this->getTranslator();
        $mapper = $this->getGarbageMapper();
        $dir = $this->getDir();
        $success  = [];
        $failures = [];
        try {
            $mapper->beginTransaction();
            $tid = $mapper->getTransactionIdentifier();
            $files = $mapper->findGarbage(self::FilesPerOperation, $tid);
            foreach ($files as $file) {
                $name = $file['name'];
                list ($extension, $mime) = explode(':', $file['spec']);
                $filePath = $dir->appUploads($name . '.' . $extension, true);
                if ($filePath) {
                    if (@unlink($filePath)) {
                        $filePath = $dir->appUploads(
                            $name . '.thumbnail.' . $extension
                        );
                        @unlink($filePath);
                        $success[] = $name;
                    } else {
                        $failures[] = $name;
                    }
                } else {
                    $success[] = $name;
                }
            }
            if (! empty($failures)) {
                $mapper->registerFailures($failures, $tid);
            }
            if (! empty($success)) {
                $mapper->delete($success, $tid);
            }
            $result = ! $mapper->getGarbageAmount($tid);
            $mapper->commit();
            return $result;
        } catch (Exception $e) {
            $mapper->rollBack();
            throw new RuntimeException(
                $translator->translate(
                    'The operation failed in garbage collection.'
                ),
                null,
                $e
            );
        }
    }
}
