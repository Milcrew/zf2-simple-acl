<?php
namespace Zf2SimpleAcl;

use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zf2SimpleAcl\Guard\RouteGuard;
use Zf2SimpleAcl\Options\ModuleOptions;
use Zf2SimpleAcl\View\Strategy\RedirectionStrategy;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ServiceProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        /* @var $sm \Zend\ServiceManager\ServiceManager */
        $sm = $e->getApplication()->getServiceManager();

        $userEntityClass = $sm->get('zfcuser_module_options')->getUserEntityClass();
        $classRef = new \ReflectionClass($userEntityClass);

        if (!$classRef->implementsInterface('Zf2SimpleAcl\Entities\UserInterface')) {
            throw new \InvalidArgumentException($userEntityClass.
            ' must implement Zf2SimpleAcl\Entities\UserInterface');
        }

        $eventManager = $e->getApplication()->getEventManager();
        $eventManager->attach(new RouteGuard($sm->get('zf2simpleacl_acl'),
                                             $sm->get('zfcuserauthservice')));
        $eventManager->attach(new RedirectionStrategy($sm->get('zf2simpleacl_module_options')));
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
                'zf2simpleacl_module_options' => function ($sm) {
                    $config = $sm->get('config');
                    return new \Zf2SimpleAcl\Options\ModuleOptions(isset($config['zf2simpleacl']) ?
                                                                         $config['zf2simpleacl'] :
                                                                         array());
                },

                'zf2simpleacl_acl' => function ($sm) {
                    return new \Zf2SimpleAcl\Service\AclService($sm->get('zf2simpleacl_module_options'));
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