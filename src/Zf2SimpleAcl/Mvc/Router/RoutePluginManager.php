<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zf2SimpleAcl\Mvc\Router;

use Zend\Mvc\Router\RoutePluginManager as BasePluginManager;
use Zend\ServiceManager\ConfigInterface;

/***
 * TODO: Rebuild in future, maybe we will found more
 * suitable way.
 */
class RoutePluginManager extends BasePluginManager
{
    /**
     * @param ConfigInterface $configuration
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->setInvokableClass('part', __NAMESPACE__.'\Http\Part');
    }

    /**
     * @param string $name
     * @param string $invokableClass
     * @param null $shared
     * @return $this|BasePluginManager
     */
    public function setInvokableClass($name, $invokableClass, $shared = null)
    {
        if ($name == 'part' && $invokableClass != __NAMESPACE__.'\Http\Part' && $this->has('part')) {
           return $this;
        }

        parent::setInvokableClass($name, $invokableClass, $shared);
    }
}
