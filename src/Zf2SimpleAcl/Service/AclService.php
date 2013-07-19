<?php
namespace Zf2SimpleAcl\Service;

use Zend\Mvc\Router\SimpleRouteStack;
use Zend\Permissions\Acl\AclInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zf2SimpleAcl\Options\ModuleOptionsInterface;
use Zf2SimpleAcl\Resource\RouteResource;
use Zf2SimpleAcl\Service\Exception\DomainException;
use Zf2SimpleAcl\Role\RoleRole;

class AclService implements AclInterface
{
    /**
     * @var \Zend\Permissions\Acl\Acl
     */
    protected $acl = null;

    /**
     * @var \Zf2SimpleAcl\Options\ModuleOptionsInterface
     */
    protected $moduleOptions = null;

    /**
     * @var SimpleRouteStack
     */
    protected $routeStack = null;

    /**
     * @param ModuleOptionsInterface $moduleOptions
     * @param SimpleRouteStack $routeStack
     */
    public function __construct(ModuleOptionsInterface $moduleOptions, SimpleRouteStack $routeStack)
    {
        $this->moduleOptions = $moduleOptions;
        $this->routeStack = $routeStack;
    }

    protected function init()
    {
        /**
         * TODO: Implement cache for production
         */
        $acl = new Acl();
        $acl->{$this->moduleOptions->isPermissive() ? 'allow' : 'deny'}(null, null, null);

        $this->initRoles($acl);
        $this->initRouteRestrictions($acl);

        $this->acl = $acl;
    }

    /**
     * @param Acl $acl
     */
    protected function initRoles(Acl $acl)
    {
        $roles = $this->moduleOptions->getRoles();

        foreach ($roles as $role) {
            $acl->addRole(new RoleRole($role->getId()),
                            is_null($role->getParent()) ?
                                null :
                                new RoleRole($role->getParent()));
        }
    }

    /**
     * @param mixed $route
     * @param mixed $roles
     */
    protected function getResource($route, $roles)
    {
        $aclResource = null;

        if (!is_array($roles)) {
            $aclResource = new RouteResource($roles);
        } else if (is_array($roles) && is_string($route)) {
            $aclResource = new RouteResource($route);
        }

        return $aclResource;
    }

    /**
     * @param Acl $acl
     * @param $restriction
     * @param RoleRole $role
     * @param RouteResource $resource
     */
    protected function restrictRoute(Acl $acl, $restriction, RoleRole $role = null, RouteResource $resource = null)
    {
        if (is_null($resource)) {
            return $acl->{$restriction}($role, $resource);
        }

        $func = function ($route, array $names) use (&$func, $acl, $resource, $restriction, $role) {
            if (!count($names)) {
                throw new \DomainException("Names could not be empty");
            }

            if (!$route instanceof SimpleRouteStack) {
                if ($route instanceof \Iterator) {
                    $routes = $route;
                } else {
                    return;
                }
            } else {
                $routes = $route->getRoutes();
            }

            foreach ($routes as $name => $route) {
                $routeNames = array_merge($names, array($name));
                $parentResource = new RouteResource(join('/', $names));
                $childResource = new RouteResource(join('/', $routeNames));

                if (!$acl->hasResource($childResource)) {
                    $acl->addResource($childResource, $parentResource);
                }

                if ($acl->hasResource($resource) &&
                    $acl->inheritsResource($childResource, $resource)) {
                    $acl->{$restriction ? 'allow' : 'deny'}($role, $childResource);
                }

                if ( $route instanceof SimpleRouteStack ) {
                    $func($route->getRoutes(), $routeNames);
                }
            }
        };

        $firstPart = $resource->getFirstPart();
        if (!$acl->hasResource($firstPart)) {
            $acl->addResource(new RouteResource($firstPart), null);
        }

        $func($this->routeStack->getRoute($firstPart), array($firstPart));
        return $acl->{$restriction ? 'allow' : 'deny'}($role, $resource);
    }

    /**
     * @param Acl $acl
     * @throws Exception\DomainException
     */
    protected function initRouteRestrictions(Acl $acl)
    {
        foreach ($this->moduleOptions->getRoutes() as $route=>$roles) {
            if (!is_array($roles)) {
                $this->restrictRoute($acl, 'allow', null, new RouteResource($roles));
                continue;
            }

            if (!is_numeric($route)) {
                $aclResource = new RouteResource($route);
            } else {
                $aclResource = null;
            }
            foreach ($roles as $role=>$allow) {
                if (is_numeric($role)) {
                    $aclRole = null;
                } else {
                    $aclRole = $this->findRole($role);
                    if (is_null($aclRole)) {
                        throw new DomainException('Could not find defined role id='.$role.'.
                                                   Please make sure that you have role with current id in your
                                                   database inside role table');
                    }
                }
                
                $this->restrictRoute($acl, $allow , $aclRole, $aclResource);
            }
        }
    }


    /**
     * @param string|number $roleIdentifier
     * @return RoleRole
     */
    protected function findRole($roleIdentifier)
    {
        foreach($this->moduleOptions->getRoles() as $role) {
            if ($role->getId() == $roleIdentifier || $role->getName() == $roleIdentifier) {
                return new RoleRole($role->getId());
            }
        }
        return null;
    }

    /**
     * @param string|\Zend\Permissions\Acl\Resource\ResourceInterface $resource
     * @return bool|void
     */
    public function hasResource($resource)
    {
        if (is_null($this->acl)) {
            $this->init();
        }

        return $this->acl->hasResource($resource);
    }

    /**
     * @param  RoleInterface|string|number            $role
     * @param  ResourceInterface|string               $resource
     * @param  string                                 $privilege
     * @return bool
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        if (is_null($this->acl)) {
            $this->init();
        }

        if (!is_null($role) && !$role instanceof RoleInterface) {
            $roleEntity = $this->findRole($role);
        } else {
            $roleEntity = $role;
        }

        if (!$this->hasResource($resource)) {
            if ($this->moduleOptions->isPermissive()) {
                $resource = null;
            } else {
                return false;
            }
        }

        if (($this->acl->hasRole($roleEntity) || is_null($roleEntity))) {
            return $this->acl->isAllowed($roleEntity, $resource, $privilege);
        } else {
            return false;
        }
    }
}
