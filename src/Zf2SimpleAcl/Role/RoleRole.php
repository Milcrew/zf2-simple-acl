<?php
namespace Zf2SimpleAcl\Role;

use Zend\Permissions\Acl\Role\GenericRole;

class RoleRole extends GenericRole
{
    /**
     * @param number $roleId
     */
    public function __construct($roleId)
    {
        $this->roleId = $roleId;
    }
}
