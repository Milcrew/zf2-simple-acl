<?php
namespace Zf2SimpleAcl\Service;

use Doctrine\ORM\EntityManager;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Authentication\AuthenticationService;
use Zend\Permissions\Acl\Acl;
use Zf2SimpleAcl\Options\RestrictionOptionsInterface;
use Zf2SimpleAcl\Service\Exception\DomainException;
use Zf2SimpleAcl\Role\RoleRole;
use Zf2SimpleAcl\Role\UserRole;

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
                                RestrictionOptionsInterface $moduleOptions)
    {
        $this->authService = $authService;
        $this->defaultRoleEntity = $entityManager->find('Zf2SimpleAcl\Entities\Role', static::DEFAULT_ROLE);

        $this->initAcl($entityManager);
        $this->initConstantRestrictions($moduleOptions, $entityManager);
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

        /* @var $role \Zf2SimpleAcl\Entities\Role */
        foreach ($roles as $role) {
            $this->acl->addRole(new RoleRole($role),
                                is_null($role->getParent()) ?
                                    null :
                                    new RoleRole($role->getParent()));
        }

        $userRepository = $entityManager->getRepository('Zf2SimpleAcl\Entities\User');
        $users = $userRepository->findAll();

        /* @var $user \Zf2SimpleAcl\Entities\User */
        foreach ($users as $user) {
            $this->acl->addRole(new UserRole($user), new RoleRole($user->getRole()));
        }
    }

    /**
     * @param RestrictionOptionsInterface $restrictions
     * @param EntityManager $entityManager
     * @throws DomainException
     */
    protected function initConstantRestrictions(RestrictionOptionsInterface $restrictions,
                                                EntityManager $entityManager)
    {
        foreach ($restrictions->getRestrictions() as $resource=>$roles) {
            $aclResource = new GenericResource($resource);

            if (!$this->acl->hasResource($aclResource)) {
                $this->acl->addResource($aclResource, null);
            }

            foreach ($roles as $role=>$allow) {
                $roleEntity = $entityManager->find('Zf2SimpleAcl\Entities\Role', $role);
                if (is_null($roleEntity)) {

                    throw new DomainException('Could not find defined role id='.$role.'.
                                               Please make sure that you have role with current id in your
                                               database inside role table');
                }
                $aclRole = new RoleRole($roleEntity);
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
     * @return RoleRole
     */
    public function getAclRole()
    {
        if ($this->authService->hasIdentity()) {
            $user = $this->authService->getIdentity();
            return new RoleRole($user->getRole(), $user);
        }
        return new RoleRole($this->defaultRoleEntity);
    }

    /**
     * @return UserRole
     */
    protected function getAclUser()
    {
        if ($this->authService->hasIdentity()) {
            $user = $this->authService->getIdentity();
            return new UserRole($user);
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
                   !is_null($this->getAclUser()) &&
                   $this->acl->isAllowed($this->getAclUser(), $resource, $privilege);
        } else {
            return false;
        }
    }
}
