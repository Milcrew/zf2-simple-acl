<?php
namespace Zf2SimpleAcl\Entities;

interface UserInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * Get role
     *
     * @return \Front\Entities\Role
     */
    public function getRole();
}