<?php
namespace Zf2SimpleAcl\Service;

use Zend\Mvc\Router\RouteMatch;
use Zend\Permissions\Acl\Acl;
use Zf2SimpleAcl\Options\ModuleOptionsInterface;
use Zf2SimpleAcl\Options\RedirectRouteOptionsInterface;

class RedirectRouteService
{
    /**
     * @var \Zf2SimpleAcl\Options\RedirectRouteOptionsInterface
     */
    protected $moduleOptions = null;

    /**
     * @param ModuleOptionsInterface $moduleOptions
     */
    public function __construct(RedirectRouteOptionsInterface $moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;
    }

    /**
     * @param RouteMatch $matcher
     * @return string
     */
    public function getMatchedRoute(RouteMatch $matcher)
    {
        $routesToRedirect = $this->moduleOptions->getRedirectRoute();

        if (is_string($routesToRedirect)) {
            return $routesToRedirect;
        }

        $defaultRoute = '';
        $matchedRoute = $matcher->getMatchedRouteName();
        foreach ($routesToRedirect as $dependsOn=>$route) {
            if ($dependsOn === 0 || $dependsOn == 'default') {
                $defaultRoute = $route;
                continue;
            }
            if (preg_match('/^'.preg_quote($dependsOn).'/', $matchedRoute)) {
                return $route;
            }
        }

        return $defaultRoute;
    }
}
