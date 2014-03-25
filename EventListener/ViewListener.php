<?php

namespace Bangpound\Bundle\DrupalBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class ViewListener
 * @package Bangpound\Drupal\EventListener
 */
class ViewListener extends ContainerAware
{

    /**
     * @var RequestMatcherInterface Matches Drupal routes.
     */
    private $matcher;

    /**
     * @param RequestMatcherInterface $matcher
     */
    public function __construct(RequestMatcherInterface $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     *
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $this->container->get('bangpound_drupal.response');
        if ($this->matcher->matches($request)) {
            $router_item = $request->attributes->get('_router_item', array());
            $default_delivery_callback = (isset($router_item) && $router_item) ? $router_item['delivery_callback'] : NULL;
            $page_callback_result = $event->getControllerResult();
            drupal_deliver_page($page_callback_result, $default_delivery_callback);
            $content = ob_get_clean();
            if ($content) {
                $response->setContent($content);
            }
            $event->setResponse($response);
        }
    }
}
