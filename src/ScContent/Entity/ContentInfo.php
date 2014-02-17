<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentInfo extends AbstractEntity
{
    /**
     * @var integer
     */
    protected $authorId = 0;

    /**
     * @var string
     */
    protected $authorName = '';

    /**
     * @var string
     */
    protected $authorEmail = '';

    /**
     * @var integer
     */
    protected $editorId = 0;

    /**
     * @var string
     */
    protected $editorName = '';

    /**
     * @var string
     */
    protected $editorEmail = '';

    /**
     * @param  integer $id
     * @return void
     */
    public function setAuthorId($id)
    {
        $this->authorId = (int) $id;
    }

    /**
     * @return integer
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @param  string $name
     * @return void
     */
    public function setAuthorName($name)
    {
        $this->authorName = $name;
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param  string $email
     * @return void
     */
    public function setAuthorEmail($email)
    {
        $this->authorEmail = $email;
    }

    /**
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * @param  integer $id
     * @return void
     */
    public function setEditorId($id)
    {
        $this->editorId = (int) $id;
    }

    /**
     * @return integer
     */
    public function getEditorId()
    {
        return $this->editorId;
    }

    /**
     * @param  string $name
     * @return void
     */
    public function setEditorName($name)
    {
        $this->editorName = $name;
    }

    /**
     * @return string
     */
    public function getEditorName()
    {
        return $this->editorName;
    }

    /**
     * @param  string $email
     * @return void
     */
    public function setEditorEmail($email)
    {
        $this->editorEmail = $email;
    }

    /**
     * @return string
     */
    public function getEditorEmail()
    {
        return $this->editorEmail;
    }
}
