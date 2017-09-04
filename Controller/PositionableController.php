<?php

namespace FSi\Bundle\AdminPositionableBundle\Controller;

use FSi\Bundle\AdminBundle\Admin\CRUD\DataIndexerElement;
use FSi\Bundle\AdminBundle\Doctrine\Admin\Element;
use FSi\Bundle\AdminPositionableBundle\Event\PositionableEvent;
use FSi\Bundle\AdminPositionableBundle\Event\PositionableEvents;
use FSi\Bundle\AdminPositionableBundle\Model\PositionableInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class PositionableController
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        RouterInterface $router
    ) {
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function increasePositionAction(
        DataIndexerElement $element,
        $id,
        Request $request
    ) {
        $entity = $this->getEntity($element, $id);

        $this->eventDispatcher->dispatch(
            PositionableEvents::PRE_APPLY,
            new PositionableEvent($request, $element, $entity)
        );
        $entity->increasePosition();
        $this->eventDispatcher->dispatch(
            PositionableEvents::POST_APPLY,
            new PositionableEvent($request, $element, $entity)
        );

        $this->persistAndFlush($element, $entity);

        return $this->getRedirectResponse($element, $request);
    }

    public function decreasePositionAction(
        DataIndexerElement $element,
        $id,
        Request $request
    ) {
        $entity = $this->getEntity($element, $id);

        $this->eventDispatcher->dispatch(
            PositionableEvents::PRE_APPLY,
            new PositionableEvent($request, $element, $entity)
        );
        $entity->decreasePosition();
        $this->eventDispatcher->dispatch(
            PositionableEvents::POST_APPLY,
            new PositionableEvent($request, $element, $entity)
        );

        $this->persistAndFlush($element, $entity);

        return $this->getRedirectResponse($element, $request);
    }

    /**
     * @param DataIndexerElement $element
     * @param int $id
     * @throws RuntimeException
     * @return PositionableInterface
     */
    private function getEntity(DataIndexerElement $element, $id)
    {
        $entity = $element->getDataIndexer()->getData($id);

        if (!($entity instanceof PositionableInterface)) {
            throw new RuntimeException(
                sprintf('Entity with id %s does not implement PositionableInterface', $id)
            );
        }

        return $entity;
    }

    /**
     * @param DataIndexerElement $element
     * @param Request $request
     * @return RedirectResponse
     */
    private function getRedirectResponse(DataIndexerElement $element, Request $request)
    {
        if ($request->query->get('redirect_uri')) {
            $uri = $request->query->get('redirect_uri');
        } else {
            $uri = $this->router->generate(
                $element->getRoute(),
                $element->getRouteParameters()
            );
        }

        return new RedirectResponse($uri);
    }

    /**
     * @param Element $element
     * @param object $entity
     */
    private function persistAndFlush(Element $element, $entity)
    {
        $om = $element->getObjectManager();
        $om->persist($entity);
        $om->flush();
    }
}
