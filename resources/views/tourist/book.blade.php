<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TouriStay - Book Listing</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        body {
            background-color: #1A1A1A;
            color: #FFFFFF;
        }
        .flatpickr-day.booked {
            background: #4B5563 !important;
            color: #9CA3AF !important;
            cursor: not-allowed !important;
        }
    </style>
</head>
<body class="bg-touristay-dark text-touristay-white">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-touristay-red p-4 shadow-lg">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <h1 class="text-3xl font-bold tracking-wider">TouriStay - Book Listing</h1>
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
            <!-- Listing Details -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Book {{ $listing->location }}</h2>
                <div class="flex items-center space-x-4 mb-4">
                    @if ($listing->image)
                        <img src="{{ Storage::url($listing->image) }}" alt="{{ $listing->location }}" class="w-32 h-32 object-cover rounded-lg">
                    @else
                        <div class="w-32 h-32 bg-gray-600 rounded-lg flex items-center justify-center text-sm">No Image</div>
                    @endif
                    <div>
                        <p class="text-sm opacity-80">
                            Type: {{ $listing->typeDeLogement->name }} | 
                            Price: ${{ $listing->price }}/night | 
                            Available Until: {{ $listing->available_until ? $listing->available_until->format('Y-m-d') : 'N/A' }} | 
                            Equipment: {{ $listing->equipements->pluck('name')->join(', ') }}
                        </p>
                    </div>
                </div>

                <!-- Booking Form -->
                <form action="{{ route('tourist.book.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="annonce_id" value="{{ $listing->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="start_date" class="block text-lg">Start Date</label>
                            <input id="start_date" name="start_date" type="text" value="{{ old('start_date') }}" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green" required>
                            @error('start_date') <span class="text-touristay-red">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-lg">End Date</label>
                            <input id="end_date" name="end_date" type="text" value="{{ old('end_date') }}" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green" required>
                            @error('end_date') <span class="text-touristay-red">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-lg">Total Price: <span id="total_price">$0.00</span></p>
                    </div>
                    <button type="submit" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                        Confirm Booking
                    </button>
                </form>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-touristay-red p-4 text-center">
            <p>© 2025 TouriStay by CodeShogun. All rights reserved.</p>
        </footer>
    </div>

    <!-- Flatpickr Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bookedDates = @json($bookedDates);
            let startPicker, endPicker;

            startPicker = flatpickr("#start_date", {
                dateFormat: "Y-m-d",
                minDate: "today",
                maxDate: @json($listing->available_until ? $listing->available_until->toDateString() : null),
                disable: bookedDates,
                onChange: function(selectedDates, dateStr, instance) {
                    endPicker.set('minDate', dateStr);
                    validateDateRange();
                    calculateTotalPrice();
                },
            });

            endPicker = flatpickr("#end_date", {
                dateFormat: "Y-m-d",
                minDate: "today",
                maxDate: @json($listing->available_until ? $listing->available_until->toDateString() : null),
                disable: bookedDates,
                onChange: function(selectedDates, dateStr, instance) {
                    validateDateRange();
                    calculateTotalPrice();
                },
            });

            function validateDateRange() {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;

                if (startDate && endDate) {
                    const start = new Date(startDate);
                    const end = new Date(endDate);
                    let current = new Date(start);
                    let hasBookedDate = false;

                    while (current <= end) {
                        const dateString = current.toISOString().split('T')[0];
                        if (bookedDates.includes(dateString)) {
                            hasBookedDate = true;
                            break;
                        }
                        current.setDate(current.getDate() + 1);
                    }

                    if (hasBookedDate) {
                        alert('The selected range includes booked dates. Please choose a different range.');
                        startPicker.clear();
                        endPicker.clear();
                        document.getElementById('total_price').innerText = '$0.00';
                    }
                }
            }

            function calculateTotalPrice() {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                const pricePerNight = {{ $listing->price }};

                if (startDate && endDate) {
                    const start = new Date(startDate);
                    const end = new Date(endDate);
                    const nights = Math.ceil((end - start) / (1000 * 60 * 60 * 24));

                    if (nights > 0) {
                        const totalPrice = (pricePerNight * nights).toFixed(2);
                        document.getElementById('total_price').innerText = `$${totalPrice}`;
                    } else {
                        document.getElementById('total_price').innerText = '$0.00';
                    }
                } else {
                    document.getElementById('total_price').innerText = '$0.00';
                }
            }
        });
    </script>
</body>
</html>