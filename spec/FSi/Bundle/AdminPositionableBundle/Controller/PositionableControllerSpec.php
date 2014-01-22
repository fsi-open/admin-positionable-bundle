<?php

namespace spec\FSi\Bundle\AdminPositionableBundle\Controller;

use Doctrine\ORM\EntityManager;
use FSi\Bundle\AdminBundle\Admin\CRUD\AbstractCRUD;
use FSi\Bundle\AdminPositionableBundle\Model\PositionableInterface;
use FSi\Component\DataIndexer\DoctrineDataIndexer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin \FSi\Bundle\AdminPositionableBundle\Controller\PositionableController
 */
class PositionableControllerSpec extends ObjectBehavior
{
    function let(
        EntityManager $em,
        Router $router,
        AbstractCRUD $element,
        DoctrineDataIndexer $indexer
    ) {
        $element->getId()->willReturn('slides');
        $element->getDataIndexer()->willReturn($indexer);

        $this->beConstructedWith($em, $router);
    }

    function it_throws_runtime_exception_when_entity_doesnt_implement_proper_interface(
        AbstractCRUD $element,
        DoctrineDataIndexer $indexer
    ) {
        $indexer->getData(666)->willReturn(new \StdClass());

        $this->shouldThrow('\RuntimeException')
            ->duringIncreasePositionAction($element, 666);

        $this->shouldThrow('\RuntimeException')
            ->duringDecreasePositionAction($element, 666);
    }

    function it_throws_runtime_exception_when_specified_entity_doesnt_exist(
        AbstractCRUD $element,
        DoctrineDataIndexer $indexer
    ) {
        $indexer->getData(666)->willThrow('FSi\Component\DataIndexer\Exception\RuntimeException');

        $this->shouldThrow('FSi\Component\DataIndexer\Exception\RuntimeException')
            ->duringIncreasePositionAction($element, 666);

        $this->shouldThrow('FSi\Component\DataIndexer\Exception\RuntimeException')
            ->duringDecreasePositionAction($element, 666);
    }

    function it_decrease_position_when_decrease_position_action_called(
        AbstractCRUD $element,
        DoctrineDataIndexer $indexer,
        PositionableInterface $positionableEntity,
        EntityManager $em,
        Router $router
    ) {
        $indexer->getData(1)->willReturn($positionableEntity);

        $positionableEntity->decreasePosition()->shouldBeCalled();

        $em->persist($positionableEntity)->shouldBeCalled();
        $em->flush()->shouldBeCalled();

        $router->generate('fsi_admin_crud_list', array('element' => 'slides'))
            ->shouldBeCalled()
            ->willReturn('sample-path');

        $this->decreasePositionAction($element, 1)
            ->shouldReturnAnInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function it_increase_position_when_increase_position_action_called(
        AbstractCRUD $element,
        DoctrineDataIndexer $indexer,
        PositionableInterface $positionableEntity,
        EntityManager $em,
        Router $router
    ) {
        $indexer->getData(1)->willReturn($positionableEntity);

        $positionableEntity->increasePosition()->shouldBeCalled();

        $em->persist($positionableEntity)->shouldBeCalled();
        $em->flush()->shouldBeCalled();

        $router->generate('fsi_admin_crud_list', array('element' => 'slides'))
            ->shouldBeCalled()
            ->willReturn('sample-path');

        $this->increasePositionAction($element, 1)
            ->shouldReturnAnInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse');
    }
}
