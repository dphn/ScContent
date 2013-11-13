<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Back;

use ScContent\Mapper\Back\ContentListToggleTrashMapper,
    ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Mapper\Exception\UnavailableDestinationException,
    ScContent\Mapper\Exception\NestingException,
    ScContent\Exception\DebugException,
    //
    Zend\EventManager\EventInterface,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListRecoveryFromTrash extends ContentListAbstractListener
{
    /**#@+
     * @const string
     */
    const RecoveryFromSite         = 'Recovery from site';
    const RecoveryToSearch         = 'Recovery to search';
    const RecoveryFromTrashToTrash = 'Recovery from trash to trash';
    const SourceNotFound           = 'Source not found';
    const NestingError             = 'Nesting error';
    const DestinationNotFound      = 'Destination not found';
    const UnexpectedError          = 'Unexpected error';
    /**#@-*/

    /**
     * @var array
     */
    protected $errorMessages = array(
        self::RecoveryFromSite
            => 'A recovery operation for the content, that is not in the trash, is impossible.',

        self::RecoveryToSearch
            => 'Recovery elements to search results is impossible.',

        self::RecoveryFromTrashToTrash
            => 'Recovering elements from the trash to the trash is impossible.',

        self::SourceNotFound
            => "Unable to recovery the element with identifier '%s'. The element was not found.",

        self::NestingError
            => "The element '%s' could not be restored. Nesting error. It is impossible to place the '%s' in the '%s'.",

        self::DestinationNotFound
            => "Unable to recovery the elements. The target element with identifier '%s' was not found.",

        self::UnexpectedError
            => "Unable to recovery '%s'.",
    );

    /**
     * @param ScContent\Mapper\Back\ContentListToggleTrashMapper $mapper
     */
    public function setMapper(ContentListToggleTrashMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Recovery subtrees from the trash to the specified location
     *
     * @param Zend\EventManager\EventInterface $event
     * @return null | Zend\Http\Response
     */
    public function process(EventInterface $event)
    {
        $events = $this->getEventManager();
        $mapper = $this->getMapper();
        $optionsProvider = $this->getOptionsProvider();
        $pane = $event->getParam('pane');
        if (! $optionsProvider->hasIdentifier($pane)) {
            return;
        }
        $ids = $event->getParam('id');
        if (empty($ids)) {
            return;
        }

        $options = $optionsProvider->getOptions($pane);
        if ($options->getRoot() == 'site') {
            $this->error(self::RecoveryFromSite);
            return $this->redirect($event);
        }

        $otherOptions = $optionsProvider->getOtherOptions($pane);
        if ($otherOptions->getType() == 'search') {
            $this->error(self::RecoveryToSearch);
            return $this->redirect($event);
        }
        if ($otherOptions->getRoot() == 'trash') {
            $this->error(self::RecoveryFromTrashToTrash);
            return $this->redirect($event);
        }

        $newParentId = $otherOptions->getParent();
        foreach ($ids as $id) {
            try {
                $mapper->beginTransaction();
                $tid = $mapper->getTransactionIdentifier();
                $mapper->toggleTrash($id, $newParentId, true, $tid);
                $events->trigger(
                    __FUNCTION__ . '.recovery',
                    null,
                    array(
                        'content' => $id,
                        'tid' => $tid,
                    )
                );
                $mapper->commit();
            } catch (UnavailableSourceException $e) {
                $mapper->rollBack();
                $this->setValue($id)->error(self::SourceNotFound);
            } catch (NestingException $e) {
                $mapper->rollBack();
                $parent = $mapper->findMetaById($newParentId);
                $meta = $mapper->findMetaById($id);
                $this->setValue($meta['title'], $meta['type'], $parent['type'])
                    ->error(self::NestingError);
            } catch (UnavailableDestinationException $e) {
                $mapper->rollBack();
                $this->setValue($newParentId)->error(self::DestinationNotFound);

                // the destination is not available for all elements
                break;
            } catch (Exception $e) {
                $mapper->rollBack();
                if (DEBUG_MODE) {
                    throw new DebugException(
                        $translator->translate('Error: ') . $e->getMessage(),
                        $e->getCode(),
                        $e
                    );
                }
                $meta = $mapper->findMetaById($id);
                $name = isset($meta['title']) ? $meta['title'] : $id;
                $this->setValue($name)->error(self::UnexpectedError);
            }
        }
        return $this->redirect($event);
    }
}
