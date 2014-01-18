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

use ScContent\Entity\Back\Category,
    ScContent\Exception\RuntimeException,
    ScContent\Exception\DomainException,
    ScContent\Exception\DebugException,
    ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Mapper\Exception\UnavailableDestinationException,
    ScContent\Mapper\Exception\NestingException,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class CategoryService extends AbstractContentService
{
    /**
     * @var ScContent\Entity\Back\Category
     */
    protected $categoryPrototype;

    /**
     * @param integer $parentId
     * @throws ScContent\Exception\RuntimeException
     * @throws ScContent\Exception\DebugException
     * @return integer
     */
    public function makeCategory($parentId)
    {
        $events = $this->getEventManager();
        $mapper = $this->getContentMapper();
        $category = $this->getCategoryPrototype();
        $category = $this->prepareNew($category);
        $translator = $this->getTranslator();
        try {
            $mapper->beginTransaction();
            $tid = $mapper->getTransactionIdentifier();
            $mapper->insert($category, $parentId, $tid);
            $events->trigger(
                __FUNCTION__,
                null,
                [
                    'content' => $category,
                    'tid' => $tid,
                ]
            );
            $mapper->commit();
        } catch (UnavailableDestinationException $e) {
            $mapper->rollBack();
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "Failed to add the category. The parent element with identifier '%s' was not found."
                ),
                $parentId
            ), null, $e);
        } catch (NestingException $e) {
            $mapper->rollBack();
            $parent = $mapper->findMetaById($parentId);
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "Failed to add the category. Nesting error. You cannot add the category to the specified element type '%s'."
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
                    $e->getCode(), $e
                );
            }
            throw new RuntimeException(
                $translator->translate(
                    'An unexpected error occurred while creating the category.'
                ), null, $e
            );
        }
        return $category->getId();
    }

    /**
     * @param integer $id
     * @throws ScContent\Exception\RuntimeException
     * @throws ScContent\Exception\DebugException
     * @return ScContent\Entity\Back\Category
     */
    public function getCategory($id)
    {
        $events = $this->getEventManager();
        $mapper = $this->getContentMapper();
        $translator = $this->getTranslator();
        $category = $this->getCategoryPrototype();
        $category->setId($id);
        try {
            $mapper->beginTransaction();
            $tid = $mapper->getTransactionIdentifier();
            $mapper->findById($category);
            $events->trigger(
                __FUNCTION__,
                null,
                [
                    'content' => $category,
                    'tid' => $tid,
                ]
            );
            $mapper->commit();
        } catch (UnavailableSourceException $e) {
            $mapper->rollBack();
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "The category with identifier '%s' was not found."
                ),
                $id
            ));
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
                    'An unexpected error occurred when searching for category.'
                ),
                null,
                $e
            );
        }
        return $category;
    }

    /**
     * @throws ScContent\Exception\DomainException
     * @return ScContent\Entity\Back\Category
     */
    protected function getCategoryPrototype()
    {
        if (! $this->categoryPrototype instanceof Category) {
            $options = $this->getModuleOptions();
            $translator = $this->getTranslator();
            $categoryClass = $options->getEntityBackCategoryClass();
            $category = new $categoryClass();
            if (! $category instanceof Category) {
                throw new DomainException(sprintf(
                    $translator->translate(
                        "The custom class '%s' should inherit the class '%s'."
                    ),
                    get_class($category),
                    'ScContent\Entity\Back\Category'
                ));
            }
            $this->categoryPrototype = $category;
        }
        return $this->categoryPrototype;
    }
}
