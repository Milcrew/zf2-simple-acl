<?php
namespace Zf2SimpleAcl\Authentication;

use Zf2SimpleAcl\Entity\UserInterface;

interface AuthenticationServiceInterface
{
    /**
     * @return boolean
     */
    public function hasIdentity();

    /**
     * @return UserInterface
     */
    public function getIdentity();
}