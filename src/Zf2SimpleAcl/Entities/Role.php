<?php
namespace Zf2SimpleAcl\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Table(name="role")
 * @ORM\Entity(repositoryClass="Zf2SimpleAcl\Repositories\RoleRepository")
 */
class Role implements RoleInterface
{
    const GUEST = 1;
    const ADMIN = 4;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="role", fetch="EXTRA_LAZY")
     */
    private $users;

    /**
     * @var \Role
     *
     * @ORM\ManyToOne(targetEntity="Role", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /* (non-PHPdoc)
     * @see \Front\Entities\RoleInterface::getId()
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add users
     *
     * @param \Front\Entities\User $users
     * @return Role
     */
    public function addUser(\Front\Entities\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Front\Entities\User $users
     */
    public function removeUser(\Front\Entities\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set parent
     *
     * @param \Front\Entities\Role $parent
     * @return Role
     */
    public function setParent(\Front\Entities\Role $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Front\Entities\Role
     */
    public function getParent()
    {
        return $this->parent;
    }
}