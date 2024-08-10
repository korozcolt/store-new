<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Mail\OrderPlaced;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Title;
use Livewire\Component;
use Resend\Laravel\Facades\Resend;
use Stripe\Checkout\Session;
use Stripe\Stripe;

#[Title('Checkout')]
class CheckoutPage extends Component
{

    public $first_name;
    public $last_name;
    public $phone;
    public $email = '';
    public $street_address;
    public $city;
    public $state;
    public $zip_code;
    public $country = 'Colombia';
    public $payment_method;

    public function mount(){
        $cart_items = CartManagement::getCartItemsFromCookies();

        if(count($cart_items) == 0){
            return redirect('/products');
        }
    }

    public function placeOrder(){
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'street_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'country' => 'required',
            'payment_method' => 'required',
        ]);

        $cart_items = CartManagement::getCartItemsFromCookies();
        $lines_items = [];

        foreach ($cart_items as $item) {
            $lines_items[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $item['unit_amount'] * 100,
                    'product_data' => [
                        'name' => $item['name'],
                        'images' => [$item['images']],
                    ],
                ],
                'quantity' => $item['quantity'],
            ];
        }

        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->grand_total = CartManagement::calculateGrandTotal($cart_items);
        $order->payment_method = $this->payment_method;
        $order->payment_status = 'pending';
        $order->status = 'new';
        $order->currency = 'cop';
        $order->shipping_amount = 0;
        $order->shipping_method = 'none';
        $order->notes = 'Order placed by user '. auth()->user()->name;

        $address = new Address();
        $address->first_name = $this->first_name;
        $address->last_name = $this->last_name;
        $address->phone = $this->phone;
        $address->email = $this->email;
        $address->street_address = $this->street_address;
        $address->city = $this->city;
        $address->state = $this->state;
        $address->zip_code = $this->zip_code;
        $address->country = $this->country;

        $redirect_url = '';
        if($this->payment_method == 'stripe'){
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $sessionCheckout = Session::create([
                'payment_method_types' => ['card'],
                'customer_email' => auth()->user()->email,
                'line_items' => $lines_items,
                'mode' => 'payment',
                'success_url' => route('success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cancel'),
            ]);

            $redirect_url = $sessionCheckout->url;
        }else{
            $redirect_url = route('success');
        }

        $order->save();
        $address->order_id = $order->id;
        $address->save();
        $order->items()->createMany($cart_items);
        CartManagement::clearCartItemsFromCookies();

        //Mail::to(request()->user())->send(new OrderPlaced($order));
        Resend::emails()->send([
            'from' => config('site.name').'<'.config('site.email').'>',
            'to' => [request()->user()->email],
            'subject' => config('site.subject'),
            'html' => (new OrderPlaced($order))->render(),
        ]);
        return redirect($redirect_url);
    }

    public function render()
    {
        $cart_items = CartManagement::getCartItemsFromCookies();
        $subtotal = CartManagement::calculateSubtotal($cart_items);
        $taxes = CartManagement::calculateTaxes($cart_items);
        $shipping = 0; // Aquí debes calcular los gastos de envío según tu lógica

        $grand_total = $subtotal + $taxes + $shipping;

        return view('livewire.checkout-page', [
            'cart_items' => $cart_items,
            'sub_total' => $subtotal,
            'tax_amount' => $taxes,
            'shipping_amount' => $shipping,
            'grand_total' => $grand_total,
        ]);
    }
}
