<?php
/**
 * ScContent (https://github.com/dphn/ScContent)
 *
 * @author    Dolphin <work.dolphin@gmail.com>
 * @copyright Copyright (c) 2013 ScContent
 * @link      https://github.com/dphn/ScContent
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ScContent\Mapper\Front;

use ScContent\Mapper\AbstractContentMapper,
    ScContent\Mapper\Exception\UnavailableSourceException,
    ScContent\Entity\Front\Content,
    //
    Zend\Db\Adapter\AdapterInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class ContentMapper extends AbstractContentMapper
{
    /**
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @param ScContent\Entity\Front\Content $content
     * @throws ScContent\Mapper\Exception\UnavailableSourceException
     */
    public function findByName(Content $content)
    {
        $select = $this->getSql()->select()
            ->from($this->getTable(self::ContentTableAlias))
            ->where([
                'name' => $content->getName(),
                //'status' => 'published',
                'trash' => 0,
            ]);

        $result = $this->execute($select)->current();
        if (empty($result)) {
            throw new UnavailableSourceException(sprintf(
               "The content '%s' was not found.",
               $content->getName()
            ));
        }
        $hydrator = $this->getHydrator();
        $hydrator->hydrate($result, $content);
    }
}
