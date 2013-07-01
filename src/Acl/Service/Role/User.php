<?php
namespace Front\Acl\Role;

use Zend\Permissions\Acl\Role\GenericRole;
use Front\Entities\UserInterface;

class User extends GenericRole
{
    /**
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->roleId = $user->getId().'_'.$user->getRole()->getId();
    }
}
