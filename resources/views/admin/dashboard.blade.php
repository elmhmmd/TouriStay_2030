<!-- resources/views/admin/dashboard.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TouriStay - Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
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
                <h1 class="text-3xl font-bold tracking-wider">TouriStay Admin</h1>
                <div class="flex items-center space-x-4">
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
            <!-- Welcome Section -->
            <div class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
                <p class="text-touristay-white opacity-80">You’re managing TouriStay as an admin. Let’s keep the platform safe and thriving.</p>
            </div>

            <!-- Statistics Section -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Platform Statistics 📊</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-touristay-red rounded-lg p-4 text-center">
                        <p class="text-lg font-medium">User Registrations</p>
                        <p class="text-3xl font-bold">{{ $totalUsers }}</p>
                    </div>
                    <div class="bg-touristay-green rounded-lg p-4 text-center">
                        <p class="text-lg font-medium">Active Listings</p>
                        <p class="text-3xl font-bold">{{ $activeListings }}</p>
                    </div>
                    <div class="bg-touristay-red rounded-lg p-4 text-center">
                        <p class="text-lg font-medium">Bookings</p>
                        <p class="text-3xl font-bold">{{ $totalBookings }}</p>
                    </div>
                </div>
            </section>

            <!-- Manage Listings Section -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Manage Listings 🗑️</h2>
                <p class="mb-4 text-touristay-white opacity-80">Review and remove inappropriate or fraudulent listings to keep TouriStay safe.</p>
                @if (session('success'))
                    <p class="text-touristay-green mb-4">{{ session('success') }}</p>
                @endif
                @if ($allListings->isEmpty())
                    <p class="text-touristay-white opacity-80">No listings available.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($allListings as $listing)
                            <div class="flex justify-between items-center bg-gray-800 p-4 rounded-lg">
                                <div>
                                    <h3 class="text-lg font-medium">{{ $listing->location }}</h3>
                                    <p class="text-sm opacity-80">Posted by: {{ $listing->user->name }} | ID: {{ $listing->id }} | Type: {{ $listing->typeDeLogement->name }}</p>
                                </div>
                                <form action="{{ route('annonces.destroy', $listing->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-touristay-red hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- Bookings & Payments Tracking Section -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Recent Bookings & Payments 📅</h2>
                @if ($recentBookings->isEmpty())
                    <p class="text-touristay-white opacity-80">No recent bookings available.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-700">
                                    <th class="p-3">Booking ID</th>
                                    <th class="p-3">User</th>
                                    <th class="p-3">Listing</th>
                                    <th class="p-3">Dates</th>
                                    <th class="p-3">Total Price</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentBookings as $booking)
                                    <tr class="border-b border-gray-700">
                                        <td class="p-3">{{ $booking->id }}</td>
                                        <td class="p-3">{{ $booking->user->name }}</td>
                                        <td class="p-3">{{ $booking->annonce ? $booking->annonce->location : 'N/A' }}</td>
                                        <td class="p-3">{{ $booking->start_date->format('Y-m-d') }} to {{ $booking->end_date->format('Y-m-d') }}</td>
                                        <td class="p-3">${{ $booking->total_price }}</td>
                                        <td class="p-3">{{ ucfirst($booking->status) }}</td>
                                        <td class="p-3">{{ $booking->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <!-- Add Types of Logements -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Add Types of Logements 🏠</h2>
                <p class="mb-4 text-touristay-white opacity-80">Add multiple types of logements (e.g., Apartment, House, Villa) for proprietaire listings.</p>
                @if (session('type_success'))
                    <p class="text-touristay-green mb-4">{{ session('type_success') }}</p>
                @endif
                <form action="{{ route('types.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="types" class="block text-lg">Types (one per line)</label>
                        <textarea id="types" name="types" rows="5" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green" placeholder="Apartment\nHouse\nVilla\nCottage"></textarea>
                        @error('types') <span class="text-touristay-red">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                        Add Types
                    </button>
                </form>

                <!-- Display Existing Types -->
                <div class="mt-6">
                    <h3 class="text-xl font-semibold mb-2">Existing Types</h3>
                    @if ($types->isEmpty())
                        <p class="text-touristay-white opacity-80">No types of logements added yet.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($types as $type)
                                <li class="flex justify-between items-center bg-gray-800 p-4 rounded-lg">
                                    <span>{{ $type->name }}</span>
                                    <form action="{{ route('types.destroy', $type->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-touristay-red hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-1 rounded-lg transition duration-300">
                                            Delete
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-touristay-red p-4 text-center">
            <p>© 2025 TouriStay. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>