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

use ScContent\Mapper\Back\ContentListReorderMapper,
    ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Mapper\Exception\UnavailableDestinationException,
    ScContent\Exception\DebugException,
    //
    Zend\EventManager\EventInterface,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListReorder extends ContentListAbstractListener
{
    /**#@+
     * @const string
     */
    const ReorderSearch       = 'Reorder search';
    const ReorderTrash        = 'Reorder trash';
    const UnmanagedOrder      = 'Unmanaged order';
    const SourceNotFound      = 'Source not found';
    const DestinationNotFound = 'Destination not found';
    const UnexpectedError     = 'Unexpected error';
    /**#@-*/

    /**
     * @var array
     */
    protected $errorMessages = [
        self::ReorderSearch
            => 'Changing the order of elements in the search results is impossible.',

        self::ReorderTrash
            => 'Changing the order of elements in the trash is impossible.',

        self::UnmanagedOrder
            => 'The order of elements other than natural, is unmanaged.',

        self::SourceNotFound
            => 'Unable to change the position of the element with identifier %s. The element was not found.',

        self::DestinationNotFound
            => 'Unable to change the order of elements. The parent element was not found.',

        self::UnexpectedError
            => 'Unable to change the position of %s.',
    ];

    /**
     * @param ScContent\Mapper\Back\ContentListReorderMapper $mapper
     */
    public function setMapper(ContentListReorderMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Reorder elements
     *
     * @param Zend\EventManager\EventInterface $event
     * @return null | Zend\Http\Response
     */
    public function process(EventInterface $event)
    {
        $mapper = $this->getMapper();
        $optionsProvider = $this->getOptionsProvider();
        $translator = $this->getTranslator();
        $pane = $event->getParam('pane');
        if (! $optionsProvider->hasIdentifier($pane)) {
            return;
        }

        $options = $optionsProvider->getOptions($pane);
        if ($options->getType() == 'search') {
            $this->error(self::ReorderSearch);
            return $this->redirect($event);
        }
        if ($options->getRoot() == 'trash') {
            $this->error(self::ReorderTrash);
            return $this->redirect($event);
        }
        if ($options->getOrderBy() != 'natural') {
            $this->error(self::UnmanagedOrder);
            return $this->redirect($event);
        }

        $posNew = $event->getParam('position');
        $posOld = $event->getParam('old_position');
        if (empty($posNew) || empty($posOld)) {
            return;
        }
        $positions = array_diff_assoc($posNew, $posOld);
            foreach ($positions as $id => $position) {
                try {
                    $mapper->beginTransaction();
                    $tid = $mapper->getTransactionIdentifier();
                    $this->mapper->reorder($id, $position, $tid);
                    $mapper->commit();
                } catch (UnavailableSourceException $e) {
                    $mapper->rollBack();
                    $this->setValue($id)->error(self::SourceNotFound);
                } catch (UnavailableDestinationException $e) {
                    $mapper->rollBack();
                    $this->error(self::DestinationNotFound);

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
                    break;
                }
            }

        return $this->redirect($event);
    }
}
