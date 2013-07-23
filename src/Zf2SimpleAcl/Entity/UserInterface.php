<?php
namespace Zf2SimpleAcl\Entity;

interface UserInterface
{
    /**
     * Get role
     *
     * @return number | string
     */
    public function getRole();
}