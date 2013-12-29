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

use ScContent\Entity\Back\Article,
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
class ArticleService extends AbstractContentService
{
    /**
     * @var ScContent\Entity\Back\Article
     */
    protected $articlePrototype;

    /**
     * @param integer $parentId
     * @throws ScContent\Exception\RuntimeException
     * @throws ScContent\Exception\DebugException
     * @return integer
     */
    public function makeArticle($parentId)
    {
        $events = $this->getEventManager();
        $mapper = $this->getContentMapper();
        $article = $this->getArticlePrototype();
        $article = $this->prepareNew($article);
        $translator = $this->getTranslator();
        try {
            $mapper->beginTransaction();
            $tid = $mapper->getTransactionIdentifier();
            $mapper->insert($article, $parentId, $tid);
            $events->trigger(
                __FUNCTION__,
                null,
                [
                    'content' => $article,
                    'tid' => $tid,
                ]
            );
            $mapper->commit();
        } catch (UnavailableDestinationException $e) {
            $mapper->rollBack();
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "Failed to add the article. The parent element with identifier '%s' was not found."
                ),
                $parentId
            ), null, $e);
        } catch (NestingException $e) {
            $mapper->rollBack();
            $parent = $mapper->findMetaById($parentId);
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "Failed to add the article. Nesting error. You cannot add the article to the specified element type '%s'."
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
                    'Unexpected error when creating an article.'
                ),
                null,
                $e
            );
        }
        return $article->getId();
    }

    /**
     * @param integer $id
     * @throws ScContent\Exception\RuntimeExcception
     * @throws ScContent\Exception\DebugException
     * @return ScContent\Entity\Back\Article
     */
    public function getArticle($id)
    {
        $events = $this->getEventManager();
        $mapper = $this->getContentMapper();
        $translator = $this->getTranslator();
        $article = $this->getArticlePrototype();
        $article->setId($id);
        try {
            $mapper->beginTransaction();
            $tid = $mapper->getTransactionIdentifier();
            $mapper->findById($article);
            $events->trigger(
                __FUNCTION__,
                null,
                [
                    'content' => $article,
                    'tid' => $tid
                ]
            );
            $mapper->commit();
        } catch (UnavailableSourceException $e) {
            $mapper->rollBack();
            throw new RuntimeException(sprintf(
                $translator->translate(
                    "The article with identifier '%s' was not found."
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
                    'An unexpected error occurred while searching for the article.'
                ),
                null,
                $e
            );
        }
        return $article;
    }

    /**
     * @throws ScContent\Exception\DomainException
     * @return ScContent\Entity\Back\Article
     */
    protected function getArticlePrototype()
    {
        if (! $this->articlePrototype instanceof Article) {
            $options = $this->getModuleOptions();
            $translator = $this->getTranslator();
            $articleClass = $options->getEntityBackArticleClass();
            $article = new $articleClass();
            if (! $article instanceof Article) {
                throw new DomainException(sprintf(
                    $translator->translate(
                        "The custom class '%s' should inherit the class '%s'."
                    ),
                    get_class($article),
                    'ScContent\Entity\Back\Article'
                ));
            }
            $this->articlePrototype = $article;
        }
        return $this->articlePrototype;
    }
}
