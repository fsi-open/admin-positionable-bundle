<?php

namespace FSi\Bundle\AdminPositionableBundle\Controller;

use Doctrine\ORM\EntityManager;
use FSi\Bundle\AdminBundle\Admin\CRUD\AbstractCRUD;
use FSi\Bundle\AdminPositionableBundle\Model\PositionableInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;

class PositionableController
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param EntityManager $em
     * @param Router $router
     */
    public function __construct(EntityManager $em, Router $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * @param AbstractCRUD $element
     * @param $id
     * @return RedirectResponse
     */
    public function increasePositionAction(AbstractCRUD $element, $id)
    {
        $entity = $this->getEntity($element, $id);
        $entity->increasePosition();

        $this->em->persist($entity);
        $this->em->flush();

        return new RedirectResponse(
            $this->router->generate('fsi_admin_crud_list', array('element' => $element->getId()))
        );
    }

    /**
     * @param AbstractCRUD $element
     * @param $id
     * @return RedirectResponse
     */
    public function decreasePositionAction(AbstractCRUD $element, $id)
    {
        $entity = $this->getEntity($element, $id);
        $entity->decreasePosition();

        $this->em->persist($entity);
        $this->em->flush();

        return new RedirectResponse(
            $this->router->generate('fsi_admin_crud_list', array('element' => $element->getId()))
        );
    }

    /**
     * @param AbstractCRUD $element
     * @param int $id
     * @throws \RuntimeException
     * @return PositionableInterface
     */
    private function getEntity(AbstractCRUD $element, $id)
    {
        $entity = $element->getDataIndexer()->getData($id);

        if (!$entity instanceof PositionableInterface) {
            throw new \RuntimeException('Entity with id %s does not implement PositionableInterface');
        }

        return $entity;
    }
}
