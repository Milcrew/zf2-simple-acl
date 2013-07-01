<?php
namespace Zf2SimpleAcl\View\Strategy;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zf2SimpleAcl\Guard\RouteGuard;

class RedirectionStrategy implements ListenerAggregateInterface
{
    /**
     * @var string route to be used to handle redirects
     */
    protected $redirectRoute = 'main';

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * {@inheritDoc}
    */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), -5000);
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
        $router     = $event->getRouter();
        $error      = $event->getError();

        if ( $result instanceof Response || ! $routeMatch  || ($response && ! $response instanceof Response) ||
             RouteGuard::ERROR_UNAUTHENTICATE !== $error
        ) {
            return;
        }
        $url = $router->assemble(array(), array('name' => $this->redirectRoute));

        $response = $response ?: new Response();

        $response->getHeaders()->addHeaderLine('Location', $url);
        $response->setStatusCode(302);

        $event->setResponse($response);
    }
}