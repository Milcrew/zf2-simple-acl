<?php
namespace Zf2SimpleAcl\Authentication;

use Zf2SimpleAcl\Authentication\Exception\RuntimeException;
use Zf2SimpleAcl\Authentication\AuthenticationServiceInterface;
use Zf2SimpleAcl\Entity\UserInterface;

class AuthenticationAggregator implements AuthenticationServiceInterface
{
    /**
     * @var AuthenticationServiceInterface[]
     */
    protected $services = null;

    public function __construct()
    {
        $this->services = new \SplObjectStorage();
    }

    /**
     * @param AuthenticationServiceInterface $service
     * @throws \Zf2SimpleAcl\Authentication\Exception\RuntimeException
     */
    public function addService(AuthenticationServiceInterface $service)
    {
        if (!$this->services->contains($service)) {
            $this->services->attach($service);
        } else {
            throw new RuntimeException('Authentication service already aggregated');
        }
    }

    /**
     * @return boolean
     */
    public function hasIdentity()
    {
        foreach ($this->services as $service) {
            if ($service->hasIdentity()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return UserInterface
     */
    public function getIdentity()
    {
        foreach ($this->services as $service) {
            if ($service->getIdentity()) {
                return $service->getIdentity();
            }
        }
        return null;
    }
}