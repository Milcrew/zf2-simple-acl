<?php
namespace Zf2SimpleAcl\Options;

use Zf2SimpleAcl\Items\RoleItem;

interface RoleOptionsInterface
{
    /**
     * @return RoleItem[]
     */
    public function getRoles();

    /**
     * @param array $role
     * @return RoleOptionsInterface
     */
    public function setRoles(array $role);
}
