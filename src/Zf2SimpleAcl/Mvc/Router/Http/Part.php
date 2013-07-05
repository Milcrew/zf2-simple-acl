<?php
namespace Zf2SimpleAcl\Mvc\Router\Http;

use Zend\Mvc\Router\RoutePluginManager;
use ArrayObject;

/**
 * Part route.
 */
class Part extends \Zend\Mvc\Router\Http\Part
{
    /**
     * @param mixed $route
     * @param bool $mayTerminate
     * @param RoutePluginManager $routePlugins
     * @param array $childRoutes
     * @param ArrayObject $prototypes
     */
    public function __construct($route, $mayTerminate, RoutePluginManager $routePlugins, array $childRoutes = null, ArrayObject $prototypes = null)
    {
        parent::__construct($route, $mayTerminate, $routePlugins, $childRoutes, $prototypes);

        if ($this->childRoutes !== null) {
            $this->addRoutes($this->childRoutes);
            $this->childRoutes = null;
        }
    }
}
