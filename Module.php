<?php
namespace Zf2SimpleAcl;

use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ServiceProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        /* @var $sm \Zend\ServiceManager\ServiceManager */
        $sm = $e->getApplication()->getServiceManager();

        /* @var $di \Zend\Di\Di */
        $di = $sm->get('di');
        $di->instanceManager()->addSharedInstance($sm->get('Doctrine\ORM\EntityManager'),
                                                  'Doctrine\ORM\EntityManager');

        $config = $sm->get('config');
        $di->instanceManager()->setParameters('Zf2SimpleAcl\Options\ModuleOptions',
                                              isset($config['zf2simpleacl']) ? $config['zf2simpleacl'] : array());
        $di->instanceManager()->setTypePreference('Zf2SimpleAcl\Options\RestrictionOptionsInterface',
                                                  array('Zf2SimpleAcl\Options\ModuleOptions'));

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
            'aliases' => array(
                'zfcuser_doctrine_em' => 'doctrine.entitymanager.orm_default',
            ),
            'factories' => array(
                'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',

                'zfcuser_module_options' => function ($sm) {
                    $config = $sm->get('Configuration');
                    return new \Zf2SimpleAcl\Options\ZfcUserOptions(isset($config['zfcuser']) ?
                                                                          $config['zfcuser'] :
                                                                          array());
                },

                'zfcuser_user_mapper' => function ($sm) {
                    return new \Zf2SimpleAcl\Mapper\User(
                        $sm->get('zfcuser_doctrine_em'),
                        $sm->get('zfcuser_module_options')
                    );
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