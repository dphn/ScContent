<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Back;

use ScContent\Validator\Mapper\NestingValidator,
    ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Mapper\Exception\UnavailableDestinationException,
    ScContent\Mapper\Exception\NestingException,
    ScContent\Mapper\Exception\LoopException,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListMoveMapper extends ContentListOperationAbstract
{
    /**
     * @var ScContent\Validator\Mapper\NestingValidator
     */
    protected $nestingValidator;

    /**
     * Constructor
     *
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     * @param ScContent\Validator\Mapper\NestingValidator
     */
    public function __construct(
        AdapterInterface $adapter,
        NestingValidator $validator
    ) {
        $this->setAdapter($adapter);
        $this->nestingValidator = $validator;
    }

    /**
     * @param integer $id Content identifier
     * @param integer $newParentId New parent content identifier
     * @param string $tid Transaction identifier
     * @throws ScContent\Mapper\Exception\UnavailableSourceException
     * @throws ScContent\Mapper\Exception\UnavailableDestinationException
     * @throws ScContent\Mapper\Exception\NestingException
     * @throws ScContent\Mapper\Exception\LoopException
     * @return void
     */
    public function move($id, $newParentId, $tid)
    {
        $this->checkTransaction($tid);

        $source = $this->findMetaById($id);
        if (empty($source)) {
            throw new UnavailableSourceException(
                'Could not find the source element.'
            );
        }
        if ($source['trash']) {
            throw new UnavailableSourceException(
                'Unable to perform the move operation for the elements in the trash.'
            );
        }
        if ($newParentId > 0) {
            $destination = $this->findMetaById($newParentId);
        } else {
            $destination = $this->getVirtualRoot(false);
        }
        if (empty($destination)) {
            throw new UnavailableDestinationException(
                'Could not find the destination element.'
            );
        }
        if ($destination['trash']) {
            throw new UnavailableDestinationException(
                'Unable to perform the move operation for the elements in the trash.'
            );
        }
        if ($destination['right_key'] <= $source['right_key']
            && $destination['left_key'] >= $source['left_key']
        ) {
            throw new LoopException(
                'Unable to be moved elements to a childrens chain.'
            );
        }
        if (! $this->nestingValidator->isValid(
            $source['type'], $destination['type'])
        ) {
            throw new NestingException(current(
                $this->nestingValidator->getMessages()
            ));
        }
        if ($destination['right_key'] - 1 < $source['right_key']) {
            $this->moveTop($source, $destination);
        } else {
            $this->moveBottom($source, $destination);
        }
    }

    /**
     * @param array $source
     * @param array $destination
     * @return void
     */
    protected function moveTop($source, $destination)
    {
        $nearKey = $destination['right_key'] - 1;
        $update = $this->getSql()->update()
            ->table($this->getTable(self::ContentTableAlias))
            ->set([
                'right_key' => new Expression(
                    'IF(`left_key` >= :leftKey,
                        `right_key` + :skewEdit,
                        IF(`right_key` < :leftKey,
                            `right_key` + :skewTree,
                            `right_key`
                        )
                    )'
                ),
                'level' => new Expression(
                    'IF(`left_key` >= :leftKey,
                        `level` + :skewLevel,
                        `level`
                    )'
                ),
                'left_key' => new Expression(
                    'IF(`left_key` >= :leftKey,
                        `left_key` + :skewEdit,
                        IF(`left_key` > :nearKey,
                            `left_key` + :skewTree,
                            `left_key`
                        )
                    )'
                )
            ])
            ->where([
                '`right_key` > ?' => $nearKey,
                '`left_key`  < ?' => $source['right_key'],
                '`trash`     = ?' => 0,
            ]);

        $skewLevel = $destination['level'] - $source['level'] + 1;
        $skewTree  = $source['right_key'] - $source['left_key'] + 1;
        $skewEdit  = $nearKey - $source['left_key'] + 1;

        $this->execute($update, [
            ':nearKey'   => $nearKey,
            ':leftKey'   => $source['left_key'],
            ':skewEdit'  => $skewEdit,
            ':skewTree'  => $skewTree,
            ':skewLevel' => $skewLevel,
        ]);
    }

    /**
     * @param array $source
     * @param array $destination
     * @return void
     */
    protected function moveBottom($source, $destination)
    {
        $nearKey = $destination['right_key'] - 1;
        $update = $this->getSql()->update()
            ->table($this->getTable(self::ContentTableAlias))
            ->set([
                'left_key' => new Expression(
                    'IF(`right_key` <= :rightKey,
                        `left_key` + :skewEdit,
                        IF(`left_key` > :rightKey,
                            `left_key` - :skewTree,
                            `left_key`
                        )
                    )'
                ),
                'level' => new Expression(
                    'IF(`right_key` <= :rightKey,
                        `level` + :skewLevel,
                        `level`
                    )'
                ),
                'right_key' => new Expression(
                    'IF(`right_key` <= :rightKey,
                        `right_key` + :skewEdit,
                        IF(`right_key` <= :nearKey,
                            `right_key` - :skewTree,
                            `right_key`
                        )
                    )'
                )
            ])
            ->where([
                '`right_key` > ?' => $source['left_key'],
                '`left_key` <= ?' => $nearKey,
                '`trash`     = ?' => 0,
            ]);

        $skewLevel = $destination['level'] - $source['level'] + 1;
        $skewTree  = $source['right_key'] - $source['left_key'] + 1;
        $skewEdit  = $nearKey - $source['right_key'];

        $this->execute($update, [
            ':nearKey'   => $nearKey,
            ':rightKey'  => $source['right_key'],
            ':skewEdit'  => $skewEdit,
            ':skewTree'  => $skewTree,
            ':skewLevel' => $skewLevel,
        ]);
    }
}
