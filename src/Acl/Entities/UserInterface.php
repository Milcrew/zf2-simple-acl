<?php
namespace Acl\Entities;

interface UserInterface extends \ZfcUser\Entity\UserInterface
{
    /**
     * Get role
     *
     * @return \Front\Entities\Role
     */
    public function getRole();
}