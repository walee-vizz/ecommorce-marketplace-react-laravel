<x-mail::message>
    <h1 style="text-align:center; font-size:24px;">
        Payment was Completed Successfully.
    </h1>
    @foreach ($orders as $order)
        <x-mail::table>
            <table>
                <tbody>
                    <tr>
                        <td>Seller </td>
                        <td>
                            <a href="{{ url('/') }}">
                                {{ $order->vendor?->store_name }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>Order #</td>
                        <td>{{ $order->id }}</td>
                    </tr>
                    <tr>
                        <td>Items</td>
                        <td>{{ $order?->orderItems?->count() }}</td>
                    </tr>
                    <tr>
                        <td>Order Date</td>
                        <td>{{ $order->created_at->format('Y-m-d H:i:s A') }}</td>
                    </tr>
                    <tr>
                        <td>Order Total</td>
                        <td>{{ \Illuminate\Support\Number::currency($order->total_price) }}</td>
                    </tr>
                </tbody>
            </table>
        </x-mail::table>

        <hr>

        <x-mail::table>
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderItems as $item)
                        <tr>
                            <td>
                                <table>
                                    <tbody>
                                        <tr>
                                            <td style="padding: 5px;">
                                                <img style="min-width:60px;max-width:60px;"
                                                    src="{{ $item->product->getImageForOptions($item->variation_type_option_ids) }}"
                                                    alt="{{ $item->product->title }}">
                                            </td>
                                            <td style="font-size:13px; padding:5px;">
                                                {{ $item->product->title }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ \Illuminate\Support\Number::currency($item->price) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-mail::table>
        <x-mail::button :url="$order->id">
            View order Details
        </x-mail::button>
    @endforeach
    <x-mail::subcopy>
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Necessitatibus explicabo minus iusto ratione totam
        quis, enim qui veniam laboriosam amet repellendus veritatis eius, quam, ea natus aperiam quas sunt odio?
    </x-mail::subcopy>


    <x-mail::panel>
        Thank you for having business with us.
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Necessitatibus explicabo minus iusto ratione totam
        quis, enim qui veniam laboriosam amet repellendus veritatis eius, quam, ea natus aperiam quas sunt odio?
    </x-mail::panel>

    Thanks, <br>
    {{ config('app.name') }}
</x-mail::message>
