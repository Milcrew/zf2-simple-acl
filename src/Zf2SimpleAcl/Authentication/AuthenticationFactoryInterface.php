<?php
namespace Zf2SimpleAcl\Authentication;

interface AuthenticationFactoryInterface
{
    /**
     * @return AuthenticationServiceInterface
     */
    public function getService();
}