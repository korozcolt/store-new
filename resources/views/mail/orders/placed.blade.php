<x-mail::message>
# Order Places Successfully!

Thank you for your order. Your order has been placed successfully.
We will notify you once your order has been shipped.

Your order details are as follows:
Order ID: {{ $order->id }}.
<x-mail::button :url="$url">
View Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
