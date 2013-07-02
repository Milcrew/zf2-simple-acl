<?php
namespace Zf2SimpleAcl\Options;

interface RedirectRouteOptionsInterface
{
    /**
     * @return string
     */
    public function getRedirectRoute();

    /**
     * @param string $route
     * @return RedirectRouteOptionsInterface
     */
    public function setRedirectRoute($route);
}
