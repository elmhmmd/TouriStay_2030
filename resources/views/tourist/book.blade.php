<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TouriStay - Book Listing</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #1A1A1A;
            color: #FFFFFF;
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
                <form action="{{ route('tourist.book') }}" method="POST">
                    @csrf
                    <input type="hidden" name="annonce_id" value="{{ $listing->id }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="start_date" class="block text-lg">Start Date</label>
                            <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}" min="{{ now()->toDateString() }}" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green" required>
                            @error('start_date') <span class="text-touristay-red">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="end_date" class="block text-lg">End Date</label>
                            <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" max="{{ $listing->available_until ? $listing->available_until->toDateString() : '' }}" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green" required>
                            @error('end_date') <span class="text-touristay-red">{{ $message }}</span> @enderror
                        </div>
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
</body>
</html>