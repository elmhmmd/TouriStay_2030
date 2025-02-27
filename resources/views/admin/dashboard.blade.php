<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TouriStay - Admin Dashboard</title>
    <!-- Include Tailwind CSS (Breeze compiles this) -->
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
                        <p class="text-3xl font-bold">1,234</p> <!-- Placeholder -->
                    </div>
                    <div class="bg-touristay-green rounded-lg p-4 text-center">
                        <p class="text-lg font-medium">Active Listings</p>
                        <p class="text-3xl font-bold">567</p> <!-- Placeholder -->
                    </div>
                    <div class="bg-touristay-red rounded-lg p-4 text-center">
                        <p class="text-lg font-medium">Bookings</p>
                        <p class="text-3xl font-bold">890</p> <!-- Placeholder -->
                    </div>
                </div>
            </section>

            <!-- Manage Listings Section -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Manage Listings 🗑️</h2>
                <p class="mb-4 text-touristay-white opacity-80">Review and remove inappropriate or fraudulent listings to keep TouriStay safe.</p>
                <div class="space-y-4">
                    <!-- Example Listing (Repeat as needed) -->
                    <div class="flex justify-between items-center bg-gray-800 p-4 rounded-lg">
                        <div>
                            <h3 class="text-lg font-medium">Cozy Beach Villa</h3>
                            <p class="text-sm opacity-80">Posted by: John Doe | ID: 12345</p>
                        </div>
                        <button class="bg-touristay-red hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                            Delete
                        </button>
                    </div>
                    <!-- Add more listings dynamically -->
                    <div class="flex justify-between items-center bg-gray-800 p-4 rounded-lg">
                        <div>
                            <h3 class="text-lg font-medium">Downtown Fraudulent Apartment</h3>
                            <p class="text-sm opacity-80">Posted by: Jane Smith | ID: 67890</p>
                        </div>
                        <button class="bg-touristay-red hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                            Delete
                        </button>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-touristay-red p-4 text-center">
            <p>&copy; 2025 TouriStay. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>