<?php
namespace Zf2SimpleAcl\Entities;

interface UserInterface
{
    /**
     * Get role
     *
     * @return number | string
     */
    public function getRole();
}