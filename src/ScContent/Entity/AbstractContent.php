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

use ScContent\View\Helper\FormatProviderInterface,
    ScContent\Exception\RuntimeException;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
abstract class AbstractContent extends AbstractEntity implements
    FormatProviderInterface
{
    /**
     * @var null | integer
     */
    protected $id = null;

    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var string
     */
    protected $status = 'draft';

    /**
     * @var integer
     */
    protected $trash = 0;

    /**
     * @var string
     */
    protected $title = 'Untitled';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var integer
     */
    protected $created = 0;

    /**
     * @var integer
     */
    protected $modified = 0;

    /**
     * @var integer
     */
    protected $author = 0;

    /**
     * @var integer
     */
    protected $editor = 0;

    /**
     * @var string
     */
    protected $spec = '';

    /**
     * @var ScContent\Entity\ContentInfo
     */
    protected $info;

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        $array = parent::getArrayCopy();
        unset($array['info']);
        return $array;
    }

    /**
     * @param integer $id
     * @return AbstractContent
     */
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $type
     * @throws ScContent\Exception\RuntimeException
     * @return AbstractContent
     */
    public function setType($type)
    {
        if ($this->type != $type) {
            throw new RuntimeException(sprintf(
               "Invalid operation on the change in the type of content with identifier '%s' from '%s' to '%s'.",
               $this->id, $this->type, $type
            ));
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $status
     * @return AbstractContent
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param boolean | integer | string $flag
     * @throws ScContent\Exception\RuntimeException
     * @return AbstractContent
     */
    public function setTrash($flag)
    {
        $flag = (int) $flag;
        if ($flag) {
            throw new RuntimeException(sprintf(
                "Unable to edit the content with identifier '%s', since it is located in the trash.",
                $this->id
            ));
        }
        return $this;
    }

    /**
     * @return integer
     */
    public function getTrash()
    {
        return $this->trash;
    }

    /**
     * @param string $title
     * @return AbstractContent
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $name
     * @return AbstractContent
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $content
     * @return AbstractContent
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $description
     * @return AbstractContent
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param integer $date GMT
     * @return AbstractContent
     */
    public function setCreated($date)
    {
        $this->created = (int) $date;
        return $this;
    }

    /**
     * @return integer GMT
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param integer $date GMT
     * @return AbstractContent
     */
    public function setModified($date)
    {
        $this->modified = (int) $date;
        return $this;
    }

    /**
     * @return integer GMT
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param integer $id
     * @return AbstractContent
     */
    public function setAuthor($id)
    {
        $this->author = (int) $id;
        return $this;
    }

    /**
     * @return integer
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param integer $id
     * @return AbstractContent
     */
    public function setEditor($id)
    {
        $this->editor = (int) $id;
        return $this;
    }

    /**
     * @return integer
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * @param string $spec
     * @return AbstractContent
     */
    public function setSpec($spec)
    {
        $this->spec = $spec;
        return $this;
    }

    /**
     * @return string
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @param ScContent\Entity\ContentInfo $info
     * @return AbstractContent
     */
    public function setInfo(ContentInfo $info)
    {
        $this->info = $info;
        return $this;
    }

    /**
     * @return ScContent\Entity\ContentInfo
     */
    public function getInfo()
    {
        return $this->info;
    }
}
