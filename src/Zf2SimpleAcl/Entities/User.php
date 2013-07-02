<?php
namespace Zf2SimpleAcl\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 */
class User implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="smallint", nullable=true, options={"unsigned"=true})
     */
    private $role;

    /**
     * Set role
     *
     * @param integer $role
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    /**
     * Get role
     *
     * @return integer
     */
    public function getRole()
    {
        return $this->role;
    }
}