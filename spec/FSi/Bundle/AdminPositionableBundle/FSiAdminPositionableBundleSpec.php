<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\FSi\Bundle\AdminPositionableBundle;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FSiAdminPositionableBundleSpec extends ObjectBehavior
{
    function it_is_bundle()
    {
        $this->shouldHaveType(Bundle::class);
    }
}
