<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Back;

use ScContent\Validator\Mapper\NestingValidator,
    ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Mapper\Exception\UnavailableDestinationException,
    ScContent\Mapper\Exception\NestingException,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Predicate\PredicateSet,
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListToggleTrashMapper extends ContentListOperationAbstract
{
    /**
     * @var ScContent\Validator\Mapper\NestingValidator
     */
    protected $nestingValidator;

    /**
     * Constructor
     *
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     * @param ScContent\Validator\Mapper\NestingValidator $validator
     */
    public function __construct(
        AdapterInterface $adapter,
        NestingValidator $validator
    ) {
        $this->setAdapter($adapter);
        $this->nestingValidator = $validator;
    }

    /**
     * @param integer $id
     * @param integer $newParentId
     * @param integer | boolean $currentTrash Trash flag
     * @param string $tid Transaction identifier
     * @throws ScContent\Mapper\Exception\UnavailableSourceException
     * @throws ScContent\Mapper\Exception\UnavailableDestinationException
     * @throws ScContent\Mapper\Exception\NestingException
     * @return void
     */
    public function toggleTrash($id, $newParentId, $currentTrash, $tid)
    {
        $this->checkTransaction($tid);

        $source = $this->findMetaById($id);
        if (empty($source)) {
            throw new UnavailableSourceException(
                'Could not find the source element.'
            );
        }
        if ($source['trash'] != $currentTrash) {
            throw new UnavailableSourceException(
                'Switching operation for a given element is not possible.'
            );
        }
        if ($newParentId == 0) {
            $destination = $this->getVirtualRoot(!$currentTrash);
        } else {
            $destination = $this->findMetaById($newParentId);
        }
        if (empty($destination) || $destination['trash'] == $currentTrash) {
            throw new UnavailableDestinationException(
                'Switching operation to the target location is not available.'
            );
        }
        if (! $this->nestingValidator->isValid(
            $source['type'], $destination['type']
        )) {
            throw new NestingException(current(
                $this->nestingValidator->getMessages()
            ));
        }
        $nearKey = $destination['right_key'] - 1;
        $update = $this->getSql()->update()
            ->table($this->getTable(self::ContentTableAlias))
            ->set(array(
                'level' => new Expression(
                    'IF(
                         `trash` = :trash AND `right_key` <= :rightKey,
                         `level` + :skewLevel,
                         `level`
                     )'
                ),
                'left_key' => new Expression(
                    '`left_key` -
                    IF(`trash` = :trash,
                        IF(`left_key` >= :leftKey,
                            IF(`right_key` <= :rightKey,
                                :skewEdit,
                                :skewTree
                            ),
                            0
                        ),
                        IF(`left_key` > :nearKey,
                            - :skewTree,
                            0
                        )
                    )'
                ),
                'right_key' => new Expression(
                    '`right_key` - @rightKeyOffset :=
                    IF(`trash` = :trash,
                        IF(`right_key` <= :rightKey,
                            :skewEdit,
                            :skewTree
                        ),
                        - :skewTree
                    )'
                ),
                'trash' => new Expression(
                    'IF(`right_key` + @rightKeyOffset <= :rightKey
                        AND `trash` = :trash,
                        NOT :trash,
                        `trash`
                    )'
                )
            ))
            ->where(array(
                '(`right_key` > ?' => $source['left_key'],
                '`trash`     = ?)' => $currentTrash,
            ))
            ->where(array('(`right_key` > ?' => $nearKey), PredicateSet::OP_OR)
            ->where(array('`trash`     <> ?)' => $currentTrash));

        $skewTree = $source['right_key'] - $source['left_key'] + 1;
        $skewEdit = $source['left_key'] - $destination['right_key'];
        $skewLevel = $destination['level'] + 1 - $source['level'];

        $this->execute($update, array(
            ':trash'     => $currentTrash,
            ':nearKey'   => $nearKey,
            ':leftKey'   => $source['left_key'],
            ':rightKey'  => $source['right_key'],
            ':skewTree'  => $skewTree,
            ':skewEdit'  => $skewEdit,
            ':skewLevel' => $skewLevel,
        ));
    }
}
