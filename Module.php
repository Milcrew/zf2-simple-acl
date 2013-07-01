<?php
namespace Zf2SimpleAcl;

use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        /* @var $sm \Zend\ServiceManager\ServiceManager */
        $sm = $e->getApplication()->getServiceManager();

        /* @var $di \Zend\Di\Di */
        $di = $sm->get('di');
        $di->instanceManager()->addSharedInstance($sm->get('Doctrine\ORM\EntityManager'),
                                                           'Doctrine\ORM\EntityManager');

        $eventManager = $e->getApplication()->getEventManager();

        $eventManager->attach($di->get('Zf2SimpleAcl\Guard\RouteGuard'));
        $eventManager->attach($di->get('Zf2SimpleAcl\View\Strategy\RedirectionStrategy'));
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
