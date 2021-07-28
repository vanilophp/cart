<?php

declare(strict_types=1);

/**
 * Contains the CartAdjustmentTest class.
 *
 * @copyright   Copyright (c) 2021 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2021-07-26
 *
 */

namespace Vanilo\Cart\Tests;

use Vanilo\Adjustments\Contracts\Adjustable;
use Vanilo\Cart\Facades\Cart;

class CartAdjustmentTest extends TestCase
{
    /** @test */
    public function the_cart_model_is_an_adjustable()
    {
        Cart::create();
        $this->assertInstanceOf(Adjustable::class, Cart::model());
    }
}
