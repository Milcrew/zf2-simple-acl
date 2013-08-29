<?php
namespace Zf2SimpleAcl\Authentication;

use Zend\Authentication\AuthenticationService as ZendAuthService;
use Zf2SimpleAcl\Authentication\AuthenticationServiceInterface;
use Zf2SimpleAcl\Entity\UserInterface;

class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * @var ZendAuthService
     */
    protected $authService;

    /**
     * @param ZendAuthService $authService
     */
    public function __construct(ZendAuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @return boolean
     */
    public function hasIdentity()
    {
        return $this->authService->hasIdentity();
    }

    /**
     * @return UserInterface
     */
    public function getIdentity()
    {
        return $this->authService->getIdentity();
    }
}