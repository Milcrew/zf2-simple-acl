<?php
namespace Zf2SimpleAcl\Options;

interface RouteOptionsInterface
{
    /**
     * @return array
     */
    public function getRoutes();

    /**
     * @param array $routes
     * @return RestrictionOptionsInterface
     */
    public function setRoutes(array $routes);
}
