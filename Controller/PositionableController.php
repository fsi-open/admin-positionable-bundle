<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
use Symfony\Component\HttpFoundation\Response;
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

    public function __construct(EventDispatcherInterface $eventDispatcher, RouterInterface $router)
    {
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function increasePositionAction(DataIndexerElement $element, $id, Request $request): Response
    {
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

    public function decreasePositionAction(DataIndexerElement $element, $id, Request $request): Response
    {
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
     * @param mixed $id
     * @throws RuntimeException
     * @return PositionableInterface
     */
    private function getEntity(DataIndexerElement $element, $id): PositionableInterface
    {
        $entity = $element->getDataIndexer()->getData($id);

        if (false === $entity instanceof PositionableInterface) {
            throw new RuntimeException(
                sprintf('Entity with id %s does not implement PositionableInterface', $id)
            );
        }

        return $entity;
    }

    private function getRedirectResponse(DataIndexerElement $element, Request $request): RedirectResponse
    {
        if ($request->query->get('redirect_uri')) {
            $uri = $request->query->get('redirect_uri');
        } else {
            $uri = $this->router->generate($element->getRoute(), $element->getRouteParameters());
        }

        return new RedirectResponse($uri);
    }

    /**
     * @param Element $element
     * @param object $entity
     */
    private function persistAndFlush(Element $element, $entity): void
    {
        $om = $element->getObjectManager();
        $om->persist($entity);
        $om->flush();
    }
}
