<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TouriStay - Tourist Dashboard</title>
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
                <h1 class="text-3xl font-bold tracking-wider">TouriStay Tourist</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('tourist.listings') }}" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                        Browse Listings
                    </a>
                    <a href="{{ route('tourist.favorites') }}" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                        Favorites
                    </a>
                    <a href="/profile" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                        My Profile
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
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="bg-touristay-green text-touristay-dark p-4 rounded-lg shadow-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-touristay-red text-touristay-dark p-4 rounded-lg shadow-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Welcome Section -->
            <div class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
                <p class="text-touristay-white opacity-80">You’re a Tourist on TouriStay. Check your bookings below or browse listings to find your next stay!</p>
            </div>

            <!-- Bookings Section -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Your Bookings 📅</h2>
                @if ($user->bookings->isEmpty())
                    <p class="text-touristay-white opacity-80">You haven’t made any bookings yet. <a href="{{ route('tourist.listings') }}" class="text-touristay-green hover:underline">Browse listings</a> to get started!</p>
                @else
                    <div class="space-y-4">
                        @foreach ($user->bookings as $booking)
                            <div class="flex justify-between items-center bg-gray-800 p-4 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    @if ($booking->annonce->image)
                                        <img src="{{ Storage::url($booking->annonce->image) }}" alt="{{ $booking->annonce->location }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-600 rounded-lg flex items-center justify-center text-sm">No Image</div>
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-medium">{{ $booking->annonce->location }}</h3>
                                        <p class="text-sm opacity-80">
                                            Type: {{ $booking->annonce->typeDeLogement->name }} | 
                                            Price: ${{ $booking->total_price }} | 
                                            Dates: {{ $booking->start_date->format('Y-m-d') }} to {{ $booking->end_date->format('Y-m-d') }} | 
                                            Equipment: {{ $booking->annonce->equipements->pluck('name')->join(', ') }}
                                        </p>
                                    </div>
                                </div>
                                <div>
                                    <!-- Only show Download Invoice button -->
                                    <a href="{{ route('tourist.invoice.download', $booking->id) }}" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                                        Download Invoice
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