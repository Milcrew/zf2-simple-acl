<?php
namespace Zf2SimpleAcl\Options;

interface RedirectRouteOptionsInterface
{
    /**
     * @return string | array
     */
    public function getRedirectRoute();

    /**
     * @param string | array $route
     * @return RedirectRouteOptionsInterface
     */
    public function setRedirectRoute($route);
}
