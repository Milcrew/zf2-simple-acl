<?php
namespace Zf2SimpleAcl\Service;

use Doctrine\ORM\EntityManager;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Acl;
use Zf2SimpleAcl\Service\Exception\DomainException;
use Zf2SimpleAcl\Service\Role\Role;
use Zf2SimpleAcl\Service\Role\User;

class AclService
{
    const DEFAULT_ROLE = \Zf2SimpleAcl\Entities\Role::GUEST;

    /**
     * @var \Zend\Permissions\Acl\Acl
     */
    private $acl = null;

    /**
     * @var AuthenticationService
     */
    private $authService = null;

    /**
     * @var \Front\Entities\Role
     */
    private $defaultRoleEntity = null;

    /**
     * @param AuthenticationService $authService
     * @param EntityManager $entityManager
     * @param array $restrictions
     */
    public function __construct(AuthenticationService $authService,
                                EntityManager $entityManager,
                                array $restrictions = array())
    {
        $this->authService = $authService;
        $this->defaultRoleEntity = $entityManager->find('Zf2SimpleAcl\Entities\Role', static::DEFAULT_ROLE);

        $this->initAcl($entityManager);
        $this->initConstantRestrictions($restrictions, $entityManager);
    }

    /**
     * @param EntityManager $entityManager
     */
    protected function initAcl(EntityManager $entityManager)
    {
        /*
         * TODO: Implement caching for acl object
        */
        $this->acl = new \Zend\Permissions\Acl\Acl();
        $this->acl->deny(null, null, null);

        $roleRepository = $entityManager->getRepository('Zf2SimpleAcl\Entities\Role');
        $roles = $roleRepository->findAll();

        /* @var $role \Front\Entities\Role */
        foreach ($roles as $role) {
            $this->acl->addRole(new Role($role), is_null($role->getParent()) ? null : new Role($role->getParent()));
        }

        $userRepository = $entityManager->getRepository('Zf2SimpleAcl\Entities\User');
        $users = $userRepository->findAll();

        /* @var $user \Front\Entities\User */
        foreach ($users as $user) {
            $this->acl->addRole(new User($user), new Role($user->getRole()));
        }
    }

    /**
     * @param array $restrictions
     * @param EntityManager $entityManager
     * @throws DomainException
     */
    protected function initConstantRestrictions(array $restrictions, EntityManager $entityManager)
    {
        foreach ($restrictions as $resource=>$roles) {
            $aclResource = new GenericResource($resource);

            if (!$this->acl->hasResource($aclResource)) {
                $this->acl->addResource($aclResource, null);
            }

            foreach ($roles as $role=>$allow) {
                $roleEntity = $entityManager->find('Zf2SimpleAcl\Entities\Role', $role);
                if (is_null($roleEntity)) {
                    throw new DomainException('Could not find defined role id '.$role);
                }
                $aclRole = new Role($roleEntity);
                $this->acl->{$allow ? 'allow': 'deny'}($aclRole, $aclResource);
            }
        }
    }

    /**
     * @return \Zend\Permissions\Acl\Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * @return RoleInterface
     */
    public function getAclRole()
    {
        if ($this->authService->hasIdentity()) {
            $user = $this->authService->getIdentity();
            return new Role($user->getRole(), $user);
        }
        return new Role($this->defaultRoleEntity);
    }

    /**
     * @return Zf2SimpleAcl\Role\User|NULL
     */
    protected function getAclRoleUser()
    {
        if ($this->authService->hasIdentity()) {
            $user = $this->authService->getIdentity();
            return new User($user);
        }
        return null;
    }

    /**
     * @param string|\Zend\Permissions\Acl\Resource\ResourceInterface $resource
     * @param string $privilege [OPTIONAL]
     * @return boolean
     */
    public function isAllowed($resource, $privilege = null)
    {
        if ($this->acl->hasResource($resource) && $this->acl->hasRole($this->getAclRole())) {
            return $this->acl->isAllowed($this->getAclRole(), $resource, $privilege) ||
                   !is_null($this->getAclRoleUser()) &&
                   $this->acl->isAllowed($this->getAclRoleUser(), $resource, $privilege);
        } else {
            return false;
        }
    }
}
