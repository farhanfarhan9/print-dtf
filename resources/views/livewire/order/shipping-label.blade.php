{{-- resources/views/orders/shipping-label.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Label</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .label-container {
            width: 400px;
            padding: 10px;
            border: 1px solid #000;
            margin: auto;
            text-align: center;
        }
        .label-container p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="label-container">
        <h2>Shipping Label</h2>
        <p><strong>To:</strong> {{ $order->customer_name }}</p>
        <p>{{ $order->customer_address }}</p>
        <p>{{ $order->customer_city }}, {{ $order->customer_state }} {{ $order->customer_zip }}</p>

        <h3>Order Details</h3>
        <p><strong>Product:</strong> {{ $order->product_name }}</p>
        <p><strong>Quantity:</strong> {{ $order->quantity }}</p>

        <p><strong>Weight:</strong> {{ $order->weight }} kg</p>

        <h3>Sender</h3>
        <p>{{ $order->sender_name }}</p>
        <p>{{ $order->sender_address }}</p>
    </div>
</body>
</html>
