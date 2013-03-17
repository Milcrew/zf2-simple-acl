<?php
namespace ProjectStarter\Navigation\Service;

use Zend\Navigation\Service\AbstractNavigationFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Constructed factory to set pages during construction.
 */
class ConstructedNavigationFactory extends AbstractNavigationFactory
{
    /**
     * @var string|\Zend\Config\Config|array
     */
    protected $config;

    /**
     * @param string|\Zend\Config\Config|array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return array|null|\Zend\Config\Config
     */
    public function getPages(ServiceLocatorInterface $serviceLocator)
    {
        if (null === $this->pages) {
            $application = $serviceLocator->get('Application');
            $routeMatch  = $application->getMvcEvent()->getRouteMatch();
            $router      = $application->getMvcEvent()->getRouter();

            $pages = $this->getPagesFromConfig($this->config);
            $this->pages = $this->injectComponents($pages, $routeMatch, $router);
        }
        return $this->pages;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'constructed';
    }
}
