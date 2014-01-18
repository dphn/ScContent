<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013-2014 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Entity\Back;

use ScContent\Entity\AbstractEntity,
    ScContent\View\Helper\FormatProviderInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListItem extends AbstractEntity
    implements FormatProviderInterface
{
    /**
     * @var integer
     */
    protected $id = 0;

    /**
     * @var string
     */
    protected $type = 'category';

    /**
     * @var string
     */
    protected $status = 'published';

    /**
     * @var string
     */
    protected $title = 'No title.';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var integer
     */
    protected $date = 0;

    /**
     * @var string
     */
    protected $spec = '';

    /**
     * @var integer
     */
    protected $userId = 0;

    /**
     * @var string
     */
    protected $userName = '';

    /**
     * @var string
     */
    protected $userEmail = '';

    /**
     * @var integer
     */
    protected $childrens = 0;

    /**
     * @var integer
     */
    protected $order = 0;

    /**
     * @param integer $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = (int) $id;
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
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        if (! empty($title)) {
            $this->title = $title;
        }
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
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param integer $date
     * @return void
     */
    public function setDate($date)
    {
        $this->date = (int) $date;
    }

    /**
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $spec
     * @return void
     */
    public function setSpec($spec)
    {
        $this->spec = $spec;
    }

    /**
     * @return string
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @param integer $id
     * @return void
     */
    public function setUserId($id)
    {
        $this->userId = (int) $id;
    }

    /**
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setUserName($name)
    {
        if (! empty($name)) {
            $this->userName = $name;
        }
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string $email
     * @return void
     */
    public function setUserEmail($email)
    {
        $this->userEmail = $email;
    }

    /**
     * @return string
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * @param integer $count
     * @return void
     */
    public function setChildrens($count)
    {
        $this->childrens = (int) $count;
    }

    /**
     * @return integer
     */
    public function getChildrens()
    {
        return $this->childrens;
    }

    /**
     * @param integer $order
     * @return void
     */
    public function setOrder($order)
    {
        $this->order = (int) $order;
    }

    /**
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }
}
