<?php
namespace Acl\Service\Role;

use Zend\Permissions\Acl\Role\GenericRole;
use Acl\Entities\RoleInterface;

class Role extends GenericRole
{
    /**
     * @param RoleInterface $role
     */
    public function __construct(RoleInterface $role)
    {
        $this->roleId = $role->getId();
    }
}
