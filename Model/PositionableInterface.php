<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Bundle\AdminPositionableBundle\Model;

interface PositionableInterface
{
    /**
     * Increase position. When list is sorted by position ASC this method move item DOWN the list.
     *
     * @return void
     */
    public function increasePosition(): void;

    /**
     * Decrease position. When list is sorted by position ASC this method move item UP the list.
     *
     * @return void
     */
    public function decreasePosition(): void;
}
