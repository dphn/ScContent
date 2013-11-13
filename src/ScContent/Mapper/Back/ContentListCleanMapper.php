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

use Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentListCleanMapper extends ContentListOperationAbstract
{
    /**
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @param string $tid
     * @return void
     */
    public function clean($tid)
    {
        $this->checkTransaction($tid);

        $delete = $this->getSql()->delete()
            ->from($this->getTable(self::ContentTableAlias))
            ->where(array(
                'trash' => 1,
            ));

        $this->execute($delete);
    }
}
