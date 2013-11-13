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
    ScContent\Exception\DebugException,
    //
    Zend\EventManager\EventInterface,
    //
    Exception;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListMoveToTrash extends ContentListAbstractListener
{
    /**#@+
     * @const string
     */
    const MoveFromTrashToTrash  = 'Move from trash to trash';
    const SourceNotFound        = 'Source not found';
    const UnexpectedError       = 'Unexpected error';
    /**#@-*/

    /**
     * @var array
     */
    protected $errorMessages = array(
        self::MoveFromTrashToTrash
            => 'Moving elements from the trash to the trash is impossible.',

        self::SourceNotFound
            => "Unable to move the element with identifier '%s' to the trash. The element was not found.",

        self::UnexpectedError
            => "Unable to move '%s' to trash.",
    );


    /**
     * @param ScContent\Mapper\Back\ContentListToggleTrashMapper
     */
    public function setMapper(ContentListToggleTrashMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Moving subtrees in the trash
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
        $ids = $event->getParam('id');
        if (empty($ids)) {
            return;
        }

        $options = $optionsProvider->getOptions($pane);
        if ($options->getRoot() == 'trash') {
            $this->error(self::MoveFromTrashToTrash);
            return $this->redirect($event);
        }
        foreach ($ids as $id) {
            try {
                $mapper->beginTransaction();
                $tid = $mapper->getTransactionIdentifier();
                $mapper->toggleTrash($id, 0, false, $tid);
                $mapper->commit();
            } catch (UnavailableSourceException $e) {
                $mapper->rollBack();
                $this->setValue($id)->error(self::SourceNotFound);
            } catch (Exception $e) {
                $mapper->rollBack();
                if (DEBUG_MODE) {
                    throw new DebugException(
                        $translator->translate('Error: ') . $e->getMessage(),
                        $e->getCode(),
                        $e
                    );
                    $meta = $mapper->findMetaById($id);
                    $name = isset($meta['title']) ? $meta['title'] : $id;
                    $this->setValue($name)->error(self::UnexpectedError);
                }
            }
        }
        return $this->redirect($event);
    }
}
