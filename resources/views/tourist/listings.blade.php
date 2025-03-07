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
            <!-- Search Bar Section -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Find Your Stay 🌍</h2>
                <form method="GET" action="{{ route('tourist.listings') }}" class="flex gap-4">
                    <div class="flex-grow">
                        <input id="search" name="search" value="{{ request('search') }}" placeholder="Search by location, price, type..." class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                    </div>
                    <button type="submit" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                        Search
                    </button>
                </form>
            </section>

            <!-- Listings Section -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Available Listings 🏡 ({{ $listings->total() }})</h2>
                @if ($listings->isEmpty())
                    <p class="text-touristay-white opacity-80">No listings match your search.</p>
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
            <a href="{{ route('tourist.book.form', $listing->id) }}" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                Book Now
            </a>
            <form action="{{ route('tourist.favorites.add', $listing->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                    Add to Favorites
                </button>
            </form>
        </div>
    </div>
@endforeach
                    </div>
                    <!-- Pagination Links -->
                    <div class="mt-6">
                        {{ $listings->links() }}
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