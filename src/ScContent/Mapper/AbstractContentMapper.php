<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper;

use ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Entity\AbstractContent,
    ScContent\Entity\ContentInfo,
    //
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractContentMapper extends AbstractDbMapper
{
    /**
     * @const string
     */
    const ContentTableAlias = 'contentalias';

    /**
     * @const string
     */
    const UsersTableAlias = 'usersalias';

    /**
     * @const string
     */
    const SearchTableAlias = 'searchalias';

    /**
     * @var array
     */
    protected $_tables = [
        self::ContentTableAlias => 'sc_content',
        self::UsersTableAlias   => 'sc_users',
        self::SearchTableAlias  => 'sc_search',
    ];

    /**
     * @param ScContent\Entity\AbstractContent $content
     * @throws ScContent\Mapper\Exception\UnavailableSourceException
     * @return void
     */
    public function findById(AbstractContent $content)
    {
        $select = $this->getSql()->select()
            ->from([
                'content' => $this->getTable(self::ContentTableAlias),
            ])
            ->join(
                ['authors' => $this->getTable(self::UsersTableAlias)],
                'content.author = authors.user_id',
                ['author_name' => 'username', 'author_email' => 'email'],
                self::JoinLeft
            )
            ->join(
                ['editors'   => $this->getTable(self::UsersTableAlias)],
                'content.editor = editors.user_id',
                ['editor_name' => 'username', 'editor_email' => 'email'],
                self::JoinLeft
            )
            ->where([
                'id' => $content->getId(),
            ]);

        $result = $this->execute($select)->current();
        if (empty($result)) {
            throw new UnavailableSourceException(sprintf(
                "The content of the specified identifier '%s' was not found.",
                $content->getId()
            ));
        }
        $info = new ContentInfo();
        $hydrator = $this->getHydrator();
        $hydrator->hydrate($result, $content);
        $hydrator->hydrate($result, $info);
        $content->setInfo($info);
    }

    /**
     * @param integer $id
     * @return null | array;
     */
    public function findMetaById($id)
    {
        $select = $this->getSql()->select()
            ->columns([
                'id',   'left_key', 'right_key', 'level',
                'type', 'status',   'trash',     'title',
            ])
            ->from([
                'content' => $this->getTable(self::ContentTableAlias),
            ])
            ->where([
                '`content`.`id` = ?' => $id,
            ]);

        return $this->execute($select)->current();
    }

    /**
     * @param array $meta
     * @return array
     */
    protected function findBack($meta)
    {
        if (1 > $meta['level']) {
            return [];
        }
        $select = $this->getSql()->select()
            ->columns([
                'id',   'left_key', 'right_key', 'level',
                'type', 'status',   'trash',     'title',
            ])
            ->from($this->getTable(self::ContentTableAlias))
            ->where([
                '`trash`     = ?' => $meta['trash'],
                '`left_key`  < ?' => $meta['left_key'],
                '`right_key` > ?' => $meta['right_key'],
            ])
            ->order('left_key DESC');

        return $this->toArray($this->execute($select));
    }

    /**
     * @param boolean | integer $trash
     * @return array
     */
    protected function getVirtualRoot($trash)
    {
        $trash = (int) $trash;
        $root = [
            'id'        => 0,
            'left_key'  => 0,
            'right_key' => 0,
            'level'     => 0,
            'type'      => 'category',
            'status'    => 'published',
            'trash'     => $trash,
            'title'     => $trash ? 'Trash' : 'Site',
        ];

        $select = $this->getSql()->select()
            ->columns([
                'max' => new Expression('MAX(`right_key`)'),
            ])
            ->from($this->getTable(self::ContentTableAlias))
            ->where([
                '`trash` = ?' => $trash,
            ]);

        $result = $this->execute($select)->current();
        $root['right_key'] = (int) $result['max'] + 1;
        return $root;
    }
}
