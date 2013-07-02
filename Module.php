<?php
namespace Zf2SimpleAcl;

use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zf2SimpleAcl\Options\ModuleOptions;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ServiceProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        /* @var $sm \Zend\ServiceManager\ServiceManager */
        $sm = $e->getApplication()->getServiceManager();

        /* @var $di \Zend\Di\Di */
        $di = $sm->get('di');

        $userEntityClass = $sm->get('zfcuser_module_options')->getUserEntityClass();
        $classRef = new \ReflectionClass($userEntityClass);

        if (!$classRef->implementsInterface('Zf2SimpleAcl\Entities\UserInterface')) {
            throw new \InvalidArgumentException($userEntityClass.
            ' must implement Zf2SimpleAcl\Entities\UserInterface');
        }

        $di->instanceManager()->setTypePreference('Zf2SimpleAcl\Options\ModuleOptionsInterface',
                                                  array($sm->get('Zf2SimpleAcl\Options\ModuleOptions')))
                              ->setTypePreference('Zf2SimpleAcl\Options\RedirectRouteOptionsInterface',
                                                  array($sm->get('Zf2SimpleAcl\Options\ModuleOptions')));

        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach($di->get('Zf2SimpleAcl\Guard\RouteGuard'));
        $eventManager->attach($di->get('Zf2SimpleAcl\View\Strategy\RedirectionStrategy'));
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
                'Zf2SimpleAcl\Options\ModuleOptions' => function ($sm) {
                    $config = $sm->get('config');
                    return new \Zf2SimpleAcl\Options\ModuleOptions(isset($config['zf2simpleacl']) ?
                                                                         $config['zf2simpleacl'] :
                                                                         array());
                }
            )
        );
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