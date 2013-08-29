<?php
namespace Zf2SimpleAcl\Options;

use Zend\Stdlib\AbstractOptions;
use Zf2SimpleAcl\Items\RoleItem;
use Zf2SimpleAcl\Options\Exception\InvalidArgumentException;

class ModuleOptions extends AbstractOptions
    implements ModuleOptionsInterface
{
    /**
     * @var array
     */
    protected $routes = array();

    /**
     * @var RoleItem[]
     */
    protected $roles = array();

    /**
     * @var array
     */
    protected $recognizers = array();

    /**
     * @var string
     */
    protected $redirectRoute = null;

    /**
     * @var string
     */
    protected $restrictionStrategy = RestrictionStrategyOptionsInterface::PERMISSIVE_STRATEGY;

    /**
     * @return string
     */
    public function isPermissive()
    {
        return $this->restrictionStrategy == RestrictionStrategyOptionsInterface::PERMISSIVE_STRATEGY;
    }

    /**
     * @return string
     */
    public function isStrict()
    {
        return $this->restrictionStrategy == RestrictionStrategyOptionsInterface::STRICT_STRATEGY;
    }

    /**
     * @param $strategy
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setRestrictionStrategy($strategy)
    {
        if (!in_array($strategy, array(RestrictionStrategyOptionsInterface::PERMISSIVE_STRATEGY,
                                       RestrictionStrategyOptionsInterface::STRICT_STRATEGY))) {
            throw new InvalidArgumentException("Invalid restriction strategy");
        }
        $this->restrictionStrategy = $strategy;
        return $this;
    }

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
     * @return array
     */
    public function getRecognizers()
    {
        return $this->recognizers;
    }

    /**
     * @return ModuleOptions
     */
    public function setRecognizers(array $recognizers)
    {
        $this->recognizers = $recognizers;
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
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param array $routes
     * @return ModuleOptions
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
        return $this;
    }
}
