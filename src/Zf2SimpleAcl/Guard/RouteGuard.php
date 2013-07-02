<?php
namespace Zf2SimpleAcl\Guard;

use Zend\Server\Exception\InvalidArgumentException;
use Zf2SimpleAcl\Entities\UserInterface;
use Zf2SimpleAcl\Service\AclService;
use Zend\Authentication\AuthenticationService;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zf2SimpleAcl\Service\Exception\DomainException;

class RouteGuard implements ListenerAggregateInterface
{
    /**
     * Marker for invalid route errors
     */
    const ERROR = 'error-unauthorized-route';
    /**
     * Marker for unauthenticate users
     */
    const ERROR_UNAUTHENTICATE = 'error-unauthenticate-user';

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var AclService
     */
    protected $aclService = null;

    /**
     * @var AuthenticationService
     */
    protected $authService = null;

    /**
     * @param AclService $aclService
     * @param AuthenticationService $authService
     */
    public function __construct(AclService $aclService, AuthenticationService $authService)
    {
        $this->aclService = $aclService;
        $this->authService = $authService;
    }

    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), $priority);
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
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
     * @param MvcEvent $e
     */
    public function onRoute(MvcEvent $event)
    {
        $matches = $event->getRouteMatch();
        if (!$matches instanceof \Zend\Mvc\Router\RouteMatch) {
            return;
        }

        $route = $matches->getMatchedRouteName();
        if (!$route) {
            return;
        }

        /* @var $application \Zend\Mvc\ApplicationInterface */
        $application = $event->getApplication();
        if (!$this->authService->hasIdentity()) {
            if (!$this->aclService->isAllowed(null, 'route/'.$route)) {
                $event->setError(static::ERROR_UNAUTHENTICATE);
                $application->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
            }
            return;
        } else {
            $identity = $this->authService->getIdentity();
            if (!$identity instanceof UserInterface) {
                throw new \InvalidArgumentException('Identity must implement Zf2SimpleAcl\Entities\UserInterface');
            }

            if ($this->aclService->isAllowed($this->authService->getIdentity()->getRole(), 'route/'.$route)) {
                return;
            }
        }

        $event->setError(static::ERROR);
        $event->setParam('route', $route);
        $event->setParam('identity', $this->authService->getIdentity());

        $application->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
    }
}
