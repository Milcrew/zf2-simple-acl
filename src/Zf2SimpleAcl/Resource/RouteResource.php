<?php
namespace Zf2SimpleAcl\Resource;

use Zend\Permissions\Acl\Resource\GenericResource;
use Zf2SimpleAcl\Resource\Exception\InvalidArgumentException;

class RouteResource extends GenericResource
{
    /**
     * @param string $route
     */
    public function __construct($route)
    {
        if (!is_string($route)) {
            throw new InvalidArgumentException('Route resource identifier must be string');
        }
        parent::__construct($route);
    }

    /**
     * @return string
     */
    public function getFirstPart()
    {
        $parts = $this->getParts();
        return array_shift($parts);
    }

    /**
     * @return array
     */
    public function getParts()
    {
        return explode('/', $this->getResourceId());
    }
}
