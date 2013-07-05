<?php
namespace Zf2SimpleAcl\View\Strategy;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zf2SimpleAcl\Guard\RouteGuard;

class ForbiddenStrategy implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * {@inheritDoc}
    */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), -100);
    }

    /**
     * {@inheritDoc}
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Handles redirects in case of user request restricted
     * resource and does not have authentication
     *
     * @param \Zend\Mvc\MvcEvent $event
     */
    public function onDispatchError(MvcEvent $event)
    {
        // Do nothing if the result is a response object
        $result     = $event->getResult();
        $routeMatch = $event->getRouteMatch();
        $response   = $event->getResponse();
        $error      = $event->getError();

        if ( $result instanceof Response || ! $routeMatch  || ($response && ! $response instanceof Response) ||
            (RouteGuard::ERROR !== $error && RouteGuard::ERROR_UNAUTHENTICATE !== $error)
        ) {
            return;
        }

        $response = $response ?: new Response();
        $response->setStatusCode(403);

        $event->setResponse($response);

        $viewModel = new ViewModel();
        $viewModel->setTemplate('error/403');

        $model = $event->getViewModel();
        $model->addChild($viewModel);
    }
}