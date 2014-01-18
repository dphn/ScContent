<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Listener\Back;

use ScContent\Mapper\Back\ContentListMoveMapper,
    ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Mapper\Exception\UnavailableDestinationException,
    ScContent\Mapper\Exception\NestingException,
    ScContent\Mapper\Exception\LoopException,
    ScContent\Mapper\Exception\LogicException,
    ScContent\Exception\DebugException,
    //
    Zend\EventManager\EventInterface,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListMove extends ContentListAbstractListener
{
    /**#@+
     * @const string
     */
    const MoveFromTrash       = 'Move from trash';
    const MoveToTrash         = 'Move to trach';
    const MoveToSearch        = 'Move to search';
    const SourceNotFound      = 'Source not found';
    const DestinationNotFound = 'Destination not found';
    const LoopError           = 'Loop error';
    const NestingError        = 'Nesting error';
    const UnexpectedError     = 'Unexpected error';
    /**#@-*/

    /**
     * @var array
     */
    protected $errorMessages = [
        self::MoveFromTrash
            => "Moving elements from the trash is impossible. Use the 'recovery' feature.",

        self::MoveToTrash
            => "To move elements to the trash, use the 'move to trash' feature.",

        self::MoveToSearch
            => 'Moving the elements to the search results is impossible.',

        self::SourceNotFound
            => 'Unable to move the element with identifier %s. The element was not found.',

        self::DestinationNotFound
            => 'Unable to move the elements to the new location. The target element with identifier %s was not found.',

        self::LoopError
            => 'Moving the element %s to the child elements chain is impossible.',

        self::NestingError
            => 'The element %s could not be moved. Nesting error. It is impossible to place the %s in the %s.',

        self::UnexpectedError
            => 'Unable to move %s.',
    ];

    /**
     * @param ScContent\Mapper\Back\ContentListMoveMapper $mapper
     */
    public function setMapper(ContentListMoveMapper $mapper) {
        $this->mapper = $mapper;
    }

    /**
     * Moving subtrees
     *
     * @param Zend\EventManager\EventInterface $event
     * @return null | Zend\Http\Response
     */
    public function process(EventInterface $event)
    {
        $events = $this->getEventManager();
        $mapper = $this->getMapper();
        $optionsProvider = $this->getOptionsProvider();
        $translator = $this->getTranslator();
        $pane = $event->getParam('pane');
        if (! $optionsProvider->hasIdentifier($pane)) {
            return;
        }
        $ids = $event->getParam('id');
        if (empty($ids)) {
            return;
        }

        $options = $optionsProvider->getOptions($pane);
        $otherOptions = $optionsProvider->getOtherOptions($pane);
        if ($options->getRoot() == 'trash') {
            $this->error(self::MoveFromTrash);
            return $this->redirect($event);
        }
        if ($otherOptions->getType() == 'search') {
            $this->error(self::MoveToSearch);
            return $this->redirect($event);
        }
        if ($otherOptions->getRoot() == 'trash') {
            $this->error(self::MoveToTrash);
            return $this->redirect($event);
        }
        $newParentId = $otherOptions->getParent();
        foreach ($ids as $id) {
            try {
                $mapper->beginTransaction();
                $tid = $mapper->getTransactionIdentifier();
                $this->mapper->move($id, $newParentId, $tid);
                $events->trigger(
                    __FUNCTION__ . '.move',
                    null,
                    [
                        'content' => $id,
                        'tid' => $tid,
                    ]
                );
                $mapper->commit();
            } catch (UnavailableSourceException $e) {
                $mapper->rollBack();
                $this->setValue($id)->error(self::SourceNotFound);
            } catch (LoopException $e) {
                $mapper->rollBack();
                $meta = $mapper->findMetaById($id);
                $this->setValue($meta['title'])->error(self::LoopError);
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
