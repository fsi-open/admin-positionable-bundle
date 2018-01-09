<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\AdminPositionableBundle\Event;

use FSi\Bundle\AdminBundle\Admin\Element;
use FSi\Bundle\AdminPositionableBundle\Model\PositionableInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class PositionableEvent extends Event
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Element
     */
    private $element;

    /**
     * @var PositionableInterface
     */
    private $object;

    public function __construct(Request $request, Element $element, PositionableInterface $object)
    {
        $this->request = $request;
        $this->element = $element;
        $this->object = $object;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @return PositionableInterface
     */
    public function getObject()
    {
        return $this->object;
    }
}
