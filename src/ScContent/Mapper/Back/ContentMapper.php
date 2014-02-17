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

use ScContent\Mapper\AbstractContentMapper,
    ScContent\Validator\Mapper\NestingValidator,
    ScContent\Entity\AbstractContent,
    //
    ScContent\Mapper\Exception\LogicException,
    ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Mapper\Exception\UnavailableDestinationException,
    ScContent\Mapper\Exception\NestingException,
    //
    Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentMapper extends AbstractContentMapper
{
    /**
     * @var \ScContent\Validator\Mapper\NestingValidator
     */
    protected $nestingValidator;

    /**
     * Constructor
     *
     * @param \Zend\Db\Adapter\AdapterInterface
     * @param \ScContent\Validator\Mapper\NestingValidator
     */
    public function __construct(
        AdapterInterface $adapter,
        NestingValidator $validator
    ) {
        $this->setAdapter($adapter);
        $this->nestingValidator = $validator;
    }

    /**
     * @param  \ScContent\Entity\AbstractContent $entity
     * @throws \ScContent\Mapper\Exception\UnavailableSourceException
     */
    public function update(AbstractContent $entity)
    {
        $content = $this->getHydrator()->extract($entity);

        $update = $this->getSql()->update()
            ->table($this->getTable(self::ContentTableAlias))
            ->set($content)
            ->where([
                '`id`    = ?' => $content['id'],
                '`type`  = ?' => $content['type'],
                '`trash` = ?' => '0',
            ]);

        $result = $this->execute($update);

        if (! $result->getAffectedRows()) {
            throw new UnavailableSourceException(
                'Content not found.'
            );
        }

        /* Update search.
         *
         * Although the new version InnoDb support full text search,
         * this functionality is added for compatibility.
         *
         * Compensates for the lack of support transactions
         */

        $delete = $this->getSql()->delete()
            ->from($this->getTable(self::SearchTableAlias))
            ->where(['id' => $content['id']]);

        $this->execute($delete);

        $insert = $this->getSql()->insert()
            ->into($this->getTable(self::SearchTableAlias))
            ->values([
                'id'          => $content['id'],
                'name'        => $content['name'],
                'title'       => $content['title'],
                'description' => $content['description'],
                'content'     => $content['content'],
            ]);

        $this->execute($insert);
    }

    /**
     * @param  \ScContent\Entity\AbstractContent $entity
     * @param  integer $parentId
     * @param  string $tid Transaction identifier
     * @throws \ScContent\Mapper\Exception\LogicException
     * @throws \ScContent\Mapper\Exception\UnavailableDestinationException
     * @throws \ScContent\Mapper\Exception\NestingException
     */
    public function insert(AbstractContent $entity, $parentId, $tid)
    {
        $this->checkTransaction($tid);

        $content = $this->getHydrator()->extract($entity);

        if (! is_null($content['id'])) {
            throw new LogicException(
                'Unable to use insert method for existing content.'
            );
        }

        if ($parentId == 0) {
            $parent = $this->getVirtualRoot(false);
        } else {
            $parent = $this->findMetaById($parentId);
            if (empty($parent) || $parent['trash']) {
                throw new UnavailableDestinationException(
                    'Unable to add content to the specified location.'
                );
            }
            if (! $this->nestingValidator->isValid(
                    $content['type'],
                    $parent['type']
            )) {
                throw new NestingException(
                    current($this->nestingValidator->getMessages())
                );
            }
        }

        // Create nesting position.
        $content['left_key']  = $parent['right_key'];
        $content['right_key'] = $content['left_key'] + 1;
        $content['level'] = $parent['level'] + 1;

        // Preparing the tree to insert an item.
        $update = $this->getSql()->update()
            ->table($this->getTable(self::ContentTableAlias))
            ->set([
                'right_key' => new Expression('`right_key` + \'2\''),
                'left_key'  => new Expression(
                    'IF(`left_key` > :rightKey, `left_key` + \'2\', `left_key`)'
                ),
            ])
            ->where([
                '`right_key` >= ?' => $parent['right_key'],
                '`trash`      = ?' => 0
            ]);

        $this->execute($update, [':rightKey' => $parent['right_key']]);

        /* Insert content.
         */
        $insert = $this->getSql()->insert()
            ->into($this->getTable(self::ContentTableAlias))
            ->values($content);

        $this->execute($insert);
        $entity->setId($this->lastInsertId());

        /* Insert search.
         * Compensates for the lack of support transactions
         */
        $delete = $this->getSql()->delete()
            ->from($this->getTable(self::SearchTableAlias))
            ->where(['id' => $entity->getId()]);

        $this->execute($delete);

        $insert = $this->getSql()->insert()
            ->into($this->getTable(self::SearchTableAlias))
            ->values([
                'id'          => $entity->getId(),
                'title'       => $content['title'],
                'description' => $content['description'],
                'content'     => $content['content'],
            ]);

        $this->execute($insert);
    }
}
