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

use ScContent\Mapper\Exception\RuntimeException,
    //
    Zend\Db\Adapter\AdapterInterface;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class RegistrationMapper extends RolesMapper
{
    /**
     * @param  integer $userId
     * @param  string $role
     * @throws \ScContent\Mapper\Exception\RuntimeException
     * @return void
     */
    public function registerUser($userId, $role, $tid)
    {
        $this->checkTransaction($tid);

        $select = $this->getSql()->select()
            ->columns(['id'])
            ->from($this->getTable(self::RolesTableAlias))
            ->where([
                'role_id' => $role,
            ]);

        $result = $this->execute($select)->current();
        if (empty($result)) {
            throw new RuntimeException(sprintf(
                'Unknown role %s.',
                $role
            ));
        }
        $roleId = $result['id'];

        $insert = $this->getSql()->insert()
            ->into($this->getTable(self::RolesLinkerTableAlias))
            ->values([
                'user_id' => $userId,
                'role_id' => $roleId,
            ]);

        $this->execute($insert);
    }

    /**
     * @param  integer $userId
     * @return void
     */
    public function removeUser($userId)
    {
        $delete = $this->getSql()->delete()
            ->from($this->getTable(self::UsersTableAlias))
            ->where([
                'user_id' => $userId,
            ]);

        $this->execute($delete);

        $delete = $this->getSql()->delete()
            ->from($this->getTable(self::RolesLinkerTableAlias))
            ->where([
                'user_id' => $userId,
            ]);

        $this->execute($delete);
    }
}
