<?php

namespace Zf2SimpleAcl\Options;

use ZfcUser\Options\ModuleOptions as BaseModuleOptions;

class ZfcUserOptions extends BaseModuleOptions
{
    /**
     * @var string
     */
    protected $userEntityClass = 'Zf2SimpleAcl\Entity\User';
}
