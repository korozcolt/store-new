<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('My Order Detail')]
class MyOrderDetailPage extends Component
{
    public $order_id;

    public function mount($order_id)
    {
        $this->order_id = $order_id;
        $order = Order::where('id', $this->order_id)->first();
        if(!$order || $order->user_id != auth()->user()->id){
            return redirect('/my-orders');
        }
    }
    public function render()
    {
        $order_items = OrderItem::with('product')->where('order_id', $this->order_id)->get();
        $address = Address::where('order_id', $this->order_id)->first();
        $order = Order::where('id', $this->order_id)->first();

        return view('livewire.my-order-detail-page',[
            'order_items' => $order_items,
            'address' => $address,
            'order' => $order
        ]);
    }
}
