<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Service\Back;

use ScContent\Entity\Back\File,
    ScContent\Entity\Back\FilesList,
    ScContent\Exception\RuntimeException,
    ScContent\Exception\DomainException,
    ScContent\Exception\DebugException,
    ScContent\Mapper\Exception\UnavailableSource,
    ScContent\Mapper\Exception\UnavailableDestination,
    ScContent\Mapper\Exception\NestingException,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class FileService extends AbstractContentService
{
    /**
     * @var ScContent\Entity\Back\File
     */
    protected $filePrototype;

    /**
     * @param integer $parentId
     * @param array $data
     * @throws ScContent\Exception\RuntimeException
     * @return array List of file identifiers
     */
    public function makeFiles($parentId, $data)
    {
        $events = $this->getEventManager();
        $mapper = $this->getContentMapper();
        $translator = $this->getTranslator();
        $filePrototype = $this->getFilePrototype();
        $filePrototype = $this->prepareNew($filePrototype);
        $ids = [];
        try {
            $mapper->beginTransaction();
            $tid = $mapper->getTransactionIdentifier();
            foreach($data as $item) {
                $file = clone($filePrototype);
                $file->setName($item['name'])
                    ->setTitle($item['title'])
                    ->setSpec($item['spec']);

                $mapper->insert($file, $parentId, $tid);
                $events->trigger(
                    __FUNCTION__,
                    null,
                    [
                        'content' => $file,
                        'tid' => $tid,
                    ]
                );
                $ids[] = $file->getId();
            }
            $mapper->commit();
        } catch (UnavailableDestination $e) {
            $mapper->rollBack();
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "Failed to add files. The parent element with identifier '%s' was not found."
                ),
                $parentId
            ), null, $e);
        } catch (NestingException $e) {
            $mapper->rollBack();
            $parent = ['type' => 'category'];
            if ($parentId != 0) {
                $parent = $mapper->findMetaById($parentId);
            }
            $translator = $this->getTranslator();
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "Failed to add files. Nesting error. You cannot add files to the specified element type '%s'."
                ),
                $parent['type']
            ), null, $e);
        } catch (Exception $e) {
            $mapper->rollBack();
            $events->trigger(
                ERROR,
                null,
                [
                    'file'      => __FILE__,
                    'class'     => __CLASS__,
                    'method'    => __METHOD__,
                    'line'      => __LINE__,
                    'exception' => $e
                ]
            );
            if (DEBUG_MODE) {
                throw new DebugException(
                    $translator->translate('Error: ') . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
            throw new RuntimeException(
                $translator->translate(
                    'An unexpected error occurred during the registration files in the database.'
                ),
                null,
                $e
            );
        }
        return $ids;
    }

    /**
     * @param array $ids
     * @return ScContent\Entity\Back\FilesList
     */
    public function getFilesList($ids)
    {
        $events = $this->getEventManager();
        $mapper = $this->getContentMapper();
        $translator = $this->getTranslator();
        $filesList = new FilesList();
        $filePrototype = $this->getFilePrototype();
        try {
            $mapper->beginTransaction();
            $tid = $mapper->getTransactionIdentifier();
            foreach ($ids as $id) {
                $file = clone($filePrototype);
                $file->setId($id);
                try {
                    $mapper->findById($file);
                    $events->trigger(
                        __FUNCTION__,
                        null,
                        [
                            'content' => $file,
                            'tid' => $tid,
                        ]
                    );
                } catch (UnavailableSource $e) {
                    continue;
                }
                $filesList->addItem($file);
            }
            $mapper->commit();
        } catch (Exception $e) {
            $mapper->rollBack();
            $events->trigger(
                ERROR,
                null,
                [
                    'file'      => __FILE__,
                    'class'     => __CLASS__,
                    'method'    => __METHOD__,
                    'line'      => __LINE__,
                    'exception' => $e
                ]
            );
            if (DEBUG_MODE) {
                throw new DebugException(
                    $translator->translate('Error: ') . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
            throw new RuntimeException(
                $translator->translate(
                    'An unexpected error occurred when searching for files in the database.'
                ),
                null,
                $e
            );
        }
        return $filesList;
    }

    /**
     * @throws ScContent\Exception\DomainException
     * @return ScContent\Entity\Back\File
     */
    protected function getFilePrototype()
    {
        if (! $this->filePrototype instanceof File) {
            $options = $this->getModuleOptions();
            $translator = $this->getTranslator();
            $fileClass = $options->getEntityBackFileClass();
            $file = new $fileClass();
            if (! $file instanceof File) {
                throw new DomainException(sprintf(
                    $translator->translate(
                        "The custom class '%s' should inherit the class '%s'."
                    ),
                    get_class($file),
                    'ScContent\Entity\Back\File'
                ));
            }
            $this->filePrototype = $file;
        }
        return $this->filePrototype;
    }

    /**
     * @param ScContent\Entity\Back\FilesList $list
     * @return void
     */
    public function saveFiles(FilesList $list)
    {
        foreach ($list as $item) {
            $this->saveContent($item);
        }
    }
}
