<!-- resources/views/tourist/favorites.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TouriStay - My Favorites</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-touristay-dark text-touristay-white">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-touristay-red p-4 shadow-lg">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <h1 class="text-3xl font-bold">TouriStay</h1>
                <div>
                    <a href="{{ route('tourist.dashboard') }}" class="mr-4 text-touristay-green hover:underline">Dashboard</a>
                    <a href="{{ route('logout') }}" class="text-gray-600 hover:underline">Log Out</a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow max-w-7xl mx-auto p-6 space-y-6">
            <h2 class="text-2xl font-semibold">My Favorite Listings</h2>

            @if (session('success'))
                <p class="text-touristay-green">{{ session('success') }}</p>
            @endif

            @if ($user->favoriteAnnounces->isEmpty())
                <p class="text-touristay-white opacity-80">No favorite listings yet. Start adding some!</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($user->favoriteAnnounces as $annonce)
                        <div class="bg-gray-800 p-4 rounded-lg shadow-lg">
                            <h3 class="text-lg font-medium">{{ $annonce->location }}</h3>
                            <p class="text-sm opacity-80">Type: {{ $annonce->typeDeLogement->name }}</p>
                            <p class="text-sm opacity-80">Price: ${{ $annonce->price }}/night</p>
                            <p class="text-sm opacity-80">Available until: {{ $annonce->available_until }}</p>
                            <form action="{{ route('tourist.favorites.remove', $annonce->id) }}" method="POST" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-touristay-red hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg">
                                    Remove
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>

        <!-- Footer -->
        <footer class="bg-touristay-red p-4 text-center">
            <p>© 2025 TouriStay. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>