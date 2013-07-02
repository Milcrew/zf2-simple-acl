<?php
namespace Zf2SimpleAcl\Service;

use Doctrine\ORM\EntityManager;
use Zend\Permissions\Acl\AclInterface;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zf2SimpleAcl\Options\ModuleOptionsInterface;
use Zf2SimpleAcl\Options\RestrictionOptionsInterface;
use Zf2SimpleAcl\Service\Exception\DomainException;
use Zf2SimpleAcl\Role\RoleRole;
use Zf2SimpleAcl\Role\UserRole;

class AclService implements AclInterface
{
    /**
     * @var \Zend\Permissions\Acl\Acl
     */
    private $acl = null;

    /**
     * @var AuthenticationService
     */
    private $authService = null;

    /**
     * @var \Zf2SimpleAcl\Options\ModuleOptionsInterface
     */
    private $moduleOptions = null;

    /**
     * @param AuthenticationService $authService
     * @param ModuleOptionsInterface $moduleOptions
     */
    public function __construct(AuthenticationService $authService,
                                ModuleOptionsInterface $moduleOptions)
    {
        $this->authService = $authService;
        $this->moduleOptions = $moduleOptions;
    }

    /**
     *
     */
    protected function init()
    {
        $this->initAcl();
        $this->initConstantRestrictions();
    }

    protected function initAcl()
    {
        /*
         * TODO: Implement caching for acl object
        */
        $this->acl = new \Zend\Permissions\Acl\Acl();
        $this->acl->deny(null, null, null);

        $roles = $this->moduleOptions->getRoles();

        foreach ($roles as $role) {
            $this->acl->addRole(new RoleRole($role->getId()),
                                is_null($role->getParent()) ?
                                    null :
                                    new RoleRole($role->getParent()));
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
     * @param RestrictionOptionsInterface $restrictions
     * @param EntityManager $entityManager
     * @throws DomainException
     */
    protected function initConstantRestrictions()
    {
        foreach ($this->moduleOptions->getRestrictions() as $resource=>$roles) {
            if (!is_array($roles)) {
                $aclResource = new GenericResource($roles);
            } else {
                $aclResource = new GenericResource($resource);
            }

            if (!$this->acl->hasResource($aclResource)) {
                $this->acl->addResource($aclResource, null);
            }

            if (!is_array($roles)) {
                return $this->acl->allow(null, $aclResource);
            }

            foreach ($roles as $role=>$allow) {
                $aclRole = $this->findRole($role);
                if (is_null($aclRole)) {

                    throw new DomainException('Could not find defined role id='.$role.'.
                                               Please make sure that you have role with current id in your
                                               database inside role table');
                }
                $this->acl->{$allow ? 'allow': 'deny'}($aclRole, $aclResource);
            }
        }
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

        if ($this->acl->hasResource($resource) && ($this->acl->hasRole($roleEntity) || is_null($roleEntity))) {
            return $this->acl->isAllowed($roleEntity, $resource, $privilege);
        } else {
            return false;
        }
    }
}
