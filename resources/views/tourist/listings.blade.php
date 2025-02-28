<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TouriStay - Browse Listings</title>
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
                <h1 class="text-3xl font-bold tracking-wider">TouriStay - Browse Listings</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('tourist.dashboard') }}" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                        Back to Dashboard
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
            <!-- Filter Section -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Find Your Stay 🌍</h2>
                <form method="GET" action="{{ route('tourist.listings') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="location" class="block text-lg">Location</label>
                        <input id="location" name="location" value="{{ request('location') }}" placeholder="e.g., Paris" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                    </div>
                    <div>
                        <label for="min_price" class="block text-lg">Min Price</label>
                        <input id="min_price" name="min_price" type="number" step="0.01" value="{{ request('min_price') }}" placeholder="e.g., 50" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                    </div>
                    <div>
                        <label for="max_price" class="block text-lg">Max Price</label>
                        <input id="max_price" name="max_price" type="number" step="0.01" value="{{ request('max_price') }}" placeholder="e.g., 200" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                    </div>
                    <div>
                        <label for="type_de_logement_id" class="block text-lg">Type</label>
                        <select id="type_de_logement_id" name="type_de_logement_id" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                            <option value="">All Types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->id }}" {{ request('type_de_logement_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-4 flex justify-end">
                        <button type="submit" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                            Filter
                        </button>
                    </div>
                </form>
            </section>

            <!-- Listings Section -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Available Listings 🏡</h2>
                @if ($listings->isEmpty())
                    <p class="text-touristay-white opacity-80">No listings match your criteria.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($listings as $listing)
                            <div class="flex justify-between items-center bg-gray-800 p-4 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    @if ($listing->image)
                                        <img src="{{ Storage::url($listing->image) }}" alt="{{ $listing->location }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-600 rounded-lg flex items-center justify-center text-sm">No Image</div>
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-medium">{{ $listing->location }}</h3>
                                        <p class="text-sm opacity-80">
                                            Type: {{ $listing->typeDeLogement->name }} | 
                                            Price: ${{ $listing->price }}/night | 
                                            Available Until: {{ $listing->available_until ? $listing->available_until->format('Y-m-d') : 'N/A' }} | 
                                            Equipment: {{ $listing->equipements->pluck('name')->join(', ') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="space-x-2">
                                    <a href="{{ route('tourist.book', $listing->id) }}" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-touristay-red p-4 text-center">
            <p>© 2025 TouriStay by CodeShogun. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>