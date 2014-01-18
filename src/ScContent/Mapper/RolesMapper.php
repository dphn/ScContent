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

use Zend\Db\Adapter\AdapterInterface,
    Zend\Db\Sql\Expression;

/**
 * @author Dolphin <work.dolphin@gmail.com>
 */
class RolesMapper extends AbstractDbMapper
{
    /**
     * @const string
     */
    const UsersTableAlias = 'usersalias';

    /**
     * @const string
     */
    const RolesTableAlias = 'rolesalias';

    /**
     * @const string
     */
    const RolesLinkerTableAlias = 'roleslinkeralias';

    /**
     * @var array
     */
    protected $_tables = [
        self::UsersTableAlias => 'sc_users',
        self::RolesTableAlias => 'sc_roles',
        self::RolesLinkerTableAlias => 'sc_roles_linker',
    ];

    /**
     * @param Zend\Db\Adapter\AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->setAdapter($adapter);
    }

    /**
     * @param string $roleId
     * @return string
     */
    public function findRouteForRole($roleId)
    {
        $select = $this->getSql()->select()
            ->columns(['route'])
            ->from($this->getTable(self::RolesTableAlias))
            ->where([
                'role_id' => $roleId,
            ]);

        $result = $this->execute($select);

        if (! empty($result)) {
            $result = $result->current();
            return $result['route'];
        }
        return '';
    }

    /**
     * @return array
     */
    public function findRoles()
    {
        $select = $this->getSql()->select()
            ->from($this->getTable(self::RolesTableAlias));

        $result = $this->execute($select);

        return $this->toArray($result);
    }

    /**
     * @return array
     */
    public function findRegisteredRoles()
    {
        $select = $this->getSql()->select()
            ->columns(['role_id'])
            ->from($this->getTable(self::RolesTableAlias))
            ->order('id ASC');

        $result = $this->execute($select);

        return $this->toList($result, 'role_id');
    }


    /**
     * @param integer $userId
     * @return array
     */
    public function findUserRoles($userId)
    {
        $select = $this->getSql()->select()
            ->columns(['role_id'])
            ->from(['roles' => $this->getTable(self::RolesTableAlias)])
            ->join(
                ['linker' => $this->getTable(self::RolesLinkerTableAlias)],
                'roles.id = linker.role_id',
                []
            )
            ->where([
                'linker.user_id' => $userId,
            ]);

        $result = $this->execute($select);
        return $this->toList($result, 'role_id');
    }

    /**
     * @param array $options
     * @return void
     */
    public function addRole($options)
    {
        $insert = $this->getSql()->insert();
        $insert->into($this->getTable(self::RolesTableAlias))
            ->values([
                'role_id'    => $options['role_id'],
                'is_default' => $options['is_default'],
                'parent_id'  => $options['parent_id'],
                'route'      => $options['route'],
            ]);

        $this->execute($insert);
    }

    /**
     * @param string $role
     * @return integer
     */
    public function getAccountsCount($role)
    {
        $select = $this->getSql()->select()
            ->columns([
                'total' => new Expression('COUNT(`user_id`)'),
            ])
            ->from(['linker' => $this->getTable(self::RolesLinkerTableAlias)])
            ->join(
                ['roles' => $this->getTable(self::RolesTableAlias)],
                'linker.role_id = roles.id',
                [],
                self::JoinLeft
            )
            ->where([
                'roles.role_id' => $role,
            ]);

        $result = $this->execute($select)->current();
        return (int) $result['total'];
    }
}
