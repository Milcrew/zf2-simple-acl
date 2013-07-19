<?php
namespace Zf2SimpleAcl\Mvc\Service;

use Zend\Mvc\Service\AbstractPluginManagerFactory;

class RoutePluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Zf2SimpleAcl\Mvc\Router\RoutePluginManager';
}
