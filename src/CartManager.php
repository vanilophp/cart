<?php
/**
 * Contains the CartManager class.
 *
 * @copyright   Copyright (c) 2017 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2017-10-29
 *
 */


namespace Vanilo\Cart;

use Illuminate\Support\Collection;
use Vanilo\Contracts\Buyable;
use Vanilo\Cart\Contracts\CartItem;
use Vanilo\Cart\Contracts\CartManager as CartManagerContract;
use Vanilo\Cart\Models\Cart;
use Vanilo\Cart\Models\CartProxy;

class CartManager implements CartManagerContract
{
    /** @var string The key in session that holds the cart id */
    protected $sessionKey;

    /** @var  Cart  The Cart model instance */
    protected $cart;

    public function __construct()
    {
        $this->sessionKey = config('vanilo.cart.session_key');
        $this->cart = Cart::find($this->getCartId()) ?: new Cart();
    }

    /**
     * @inheritDoc
     */
    public function getItems(): Collection
    {
        return $this->cart->getItems();
    }


    /**
     * @inheritDoc
     */
    public function addItem(Buyable $product, $qty = 1, $params = []): CartItem
    {
        $this->cart = $this->findOrCreateCart();

        return $this->cart->addItem($product, $qty, $params);
    }

    /**
     * @inheritDoc
     */
    public function removeItem($item)
    {
        $this->cart->removeItem($item);
    }

    /**
     * @inheritDoc
     */
    public function removeProduct(Buyable $product)
    {
        $this->cart->removeProduct($product);
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->cart->clear();
    }

    /**
     * @inheritDoc
     */
    public function itemCount()
    {
        return $this->cart->itemCount();
    }

    /**
     * @inheritDoc
     */
    public function total()
    {
        return $this->cart->total();
    }

    /**
     * @inheritDoc
     */
    public function exists()
    {
        return (bool) $this->getCartId();
    }

    /**
     * @inheritDoc
     */
    public function doesNotExist()
    {
        return !$this->exists();
    }

    /**
     * @inheritDoc
     */
    public function model()
    {
        return $this->cart;
    }

    /**
     * @inheritDoc
     */
    public function isEmpty()
    {
        return $this->itemCount() == 0;
    }

    /**
     * @inheritDoc
     */
    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }

    /**
     * @inheritDoc
     */
    public function destroy()
    {
        $this->clear();

        $this->cart->delete();

        session()->forget($this->sessionKey);
    }


    /**
     * Returns the model id of the cart for the current session
     * or null if it does not exist
     *
     * @return int|null
     */
    protected function getCartId()
    {
        return session($this->sessionKey);
    }

    /**
     * Returns the cart model for the current session by either fetching it or creating one
     *
     * @return Cart
     */
    protected function findOrCreateCart()
    {
        return $this->exists() ? $this->cart : $this->createCart();
    }

    /**
     * Creates a new cart model and saves it's id in the session
     */
    protected function createCart()
    {
        $this->cart = CartProxy::create([]);

        session([$this->sessionKey => $this->cart->id]);

        return $this->cart;
    }
}
