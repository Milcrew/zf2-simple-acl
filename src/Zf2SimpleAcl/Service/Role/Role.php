<?php
namespace Zf2SimpleAcl\Service\Role;

use Zend\Permissions\Acl\Role\GenericRole;
use Zf2SimpleAcl\Entities\RoleInterface;

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
