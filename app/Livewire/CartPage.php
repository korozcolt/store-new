<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cart - Store New')]

class CartPage extends Component
{
    public $cart_items = [];
    public $grand_total = 0;
    public $taxes = 0;
    public $shipping = 0;

    public function mount(){
        $this->cart_items = CartManagement::getCartItemsFromCookies();
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
        $this->taxes = 0;
        $this->shipping = 0;
    }

    public function increaseQty($product_id)
    {
        $this->cart_items = CartManagement::incrementQuantityToCartItem($product_id);
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
    }

    public function decreaseQty($product_id)
    {
        $this->cart_items = CartManagement::decrementQuantityToCartItem($product_id);
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
    }

    public function removeItem($product_id){
        $this->cart_items = CartManagement::removeItemFromCart($product_id);
        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);
        $this->dispatch('updated-cart-count', total_count: count($this->cart_items))->to(Navbar::class);
    }
    public function render()
    {
        return view('livewire.cart-page');
    }
}
