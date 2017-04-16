<?php

namespace FSi\Bundle\AdminPositionableBundle\Model;

interface SortableInterface
{
    /**
     * @param int $position
     */
    public function setPosition($position);

    /**
     * @return int
     */
    public function getPosition();
}
