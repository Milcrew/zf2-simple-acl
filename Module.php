<?php
namespace Zf2SimpleAcl;

use Zf2SimpleAcl\Authentication\AuthenticationService;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zf2SimpleAcl\Authentication\AuthenticationAggregator;
use Zf2SimpleAcl\Authentication\Recognizer\CronRecognizerService;
use Zf2SimpleAcl\Exception\RuntimeException;
use Zf2SimpleAcl\Guard\RouteGuard;
use Zf2SimpleAcl\Options\ModuleOptions;
use Zf2SimpleAcl\View\Strategy\ForbiddenStrategy;
use Zf2SimpleAcl\View\Strategy\RedirectionStrategy;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ServiceProviderInterface
{
    public function onBootstrap(MvcEvent $e)
    {
        /* @var $sm \Zend\ServiceManager\ServiceManager */
        $sm = $e->getApplication()->getServiceManager();

        $userEntityClass = $sm->get('zfcuser_module_options')->getUserEntityClass();
        $classRef = new \ReflectionClass($userEntityClass);

        if (!$classRef->implementsInterface('Zf2SimpleAcl\Entity\UserInterface')) {
            throw new \InvalidArgumentException($userEntityClass.
            ' must implement Zf2SimpleAcl\Entity\UserInterface');
        }

        $eventManager = $e->getApplication()->getEventManager();


        $recognizers = $sm->get('zf2simpleacl_module_options')->getRecognizers();

        $authService = new AuthenticationService($sm->get('zfcuserauthservice'));

        if (!empty($recognizers) && count($recognizers)) {
            $authService = new AuthenticationAggregator();
            $authService->addService(new AuthenticationService($sm->get('zfcuserauthservice')));

            foreach ($recognizers as $recognizer) {
                if (!($sm->has($recognizer))) {
                    if (!($sm->has('zf2simpleacl_recognizer_'.$recognizer))) {
                        throw new RuntimeException("Could not find recognizer service with name
                                                    [$recognizer] nor with [zf2simpleacl_recognizer_$recognizer]");
                    } else {
                        $recognizer = 'zf2simpleacl_recognizer_'.$recognizer;
                    }
                }
                $authService->addService($sm->get($recognizer));
            }
        }

        $eventManager->attach(new RouteGuard($sm->get('zf2simpleacl_acl'),
                                             $authService));

        $eventManager->attach(new RedirectionStrategy($sm->get('zf2simpleacl_redirect_route')));
        $eventManager->attach(new ForbiddenStrategy());
    }

    public function getConfig()
    {
        return array(
            'view_manager' => array(
                'template_map' => array(
                    'error/403'               => __DIR__ . '/view/error/403.phtml'
                )
            )
        );
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
                    return new \Zf2SimpleAcl\Service\AclService($sm->get('zf2simpleacl_module_options'),
                                                                $sm->get('router'));
                },

                'zf2simpleacl_redirect_route' => function ($sm) {
                    return new \Zf2SimpleAcl\Service\RedirectRouteService($sm->get('zf2simpleacl_module_options'));
                },

                'zf2simpleacl_recognizer_cron' => function ($sm) {
                    return new \Zf2SimpleAcl\Authentication\Recognizer\CronRecognizerService(
                                        $sm->get('zf2simpleacl_module_options'));
                },
                
                'RoutePluginManager' => 'Zf2SimpleAcl\Mvc\Service\RoutePluginManagerFactory'
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