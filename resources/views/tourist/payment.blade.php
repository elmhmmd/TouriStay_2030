<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TouriStay - Payment</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.sandbox.client_id') }}&currency={{ config('paypal.currency') }}"></script>
    <style>
        body {
            background-color: #1A1A1A;
            color: #FFFFFF;
        }
        .paypal-buttons {
            margin-top: 20px;
        }
    </style>
</head>
<body class="bg-touristay-dark text-touristay-white">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-touristay-red p-4 shadow-lg">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <h1 class="text-3xl font-bold tracking-wider">TouriStay - Payment (Test Mode)</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('tourist.listings') }}" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                        Back to Listings
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-gray-600 hover:bg-gray-500 text-touristay-white font-semibold px-4 py-2 rounded-lg transition duration-300">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow max-w-7xl mx-auto p-6 space-y-8">
            <!-- Booking Details -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Confirm Payment for {{ $listing->location }}</h2>
                <div class="mb-4">
                    <p class="text-sm opacity-80">
                        <strong>Type:</strong> {{ $listing->typeDeLogement->name }} <br>
                        <strong>Price per Night:</strong> ${{ $listing->price }} <br>
                        <strong>Dates:</strong> {{ $booking->start_date->format('Y-m-d') }} to {{ $booking->end_date->format('Y-m-d') }} <br>
                        <strong>Total Price:</strong> ${{ $booking->total_price }} <br>
                        <strong>Equipment:</strong> {{ $listing->equipements->pluck('name')->join(', ') }}
                    </p>
                </div>

                <!-- PayPal Payment Button -->
                <div id="paypal-button-container" class="paypal-buttons"></div>
                @if ($errors->has('payment'))
                    <div class="text-touristay-red mt-2">
                        {{ $errors->first('payment') }}
                    </div>
                @endif
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-touristay-red p-4 text-center">
            <p>© 2025 TouriStay by CodeShogun. All rights reserved.</p>
        </footer>
    </div>

    <!-- PayPal Script -->
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '{{ $booking->total_price }}',
                            currency_code: '{{ config('paypal.currency') }}'
                        },
                        description: 'Booking for {{ $listing->location }}'
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Submit form to process payment
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('tourist.payment.process', $booking->id) }}';

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    const orderId = document.createElement('input');
                    orderId.type = 'hidden';
                    orderId.name = 'order_id';
                    orderId.value = data.orderID;
                    form.appendChild(orderId);

                    document.body.appendChild(form);
                    form.submit();
                });
            },
            onError: function(err) {
                alert('Payment failed: ' + err);
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>