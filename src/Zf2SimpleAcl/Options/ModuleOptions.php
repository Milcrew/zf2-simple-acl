<?php
namespace Zf2SimpleAcl\Options;

use Zend\Stdlib\AbstractOptions;
use Zf2SimpleAcl\Entities\Role;
use Zf2SimpleAcl\Items\RoleItem;

class ModuleOptions extends AbstractOptions
    implements ModuleOptionsInterface
{
    /**
     * @var array
     */
    protected $restrictions = array();

    /**
     * @var RoleItem[]
     */
    protected $roles = array();

    /**
     * @var string
     */
    protected $redirectRoute = null;

    /**
     * @return string
     */
    public function getRedirectRoute()
    {
        return $this->redirectRoute;
    }

    /**
     * @param string $redirectRoute
     * @return ModuleOptions
     */
    public function setRedirectRoute($redirectRoute)
    {
        $this->redirectRoute = $redirectRoute;
        return $this;
    }

    /**
     * @return RoleItem[]
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return ModuleOptions
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();
        foreach($roles as $role) {
            $this->roles[] = new RoleItem($role);
        }
    }

    /**
     * @return array
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }

    /**
     * @param array $restrictions
     * @return ModuleOptions
     */
    public function setRestrictions(array $restrictions)
    {
        $this->restrictions = $restrictions;
        return $this;
    }
}
