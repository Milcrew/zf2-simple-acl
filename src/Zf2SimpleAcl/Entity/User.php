<?php
namespace Zf2SimpleAcl\Entity;

class User implements UserInterface
{
    /**
     * @var number
     */
    protected $role;

    /**
     * Get role
     *
     * @return number | string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }
}