<?php
namespace Zf2SimpleAcl\Role;

use Zend\Permissions\Acl\Role\GenericRole;
use Zf2SimpleAcl\Entities\UserInterface;

class UserRole extends GenericRole
{
    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->roleId = $user->getId().'_'.$user->getRole()->getId();
    }
}
