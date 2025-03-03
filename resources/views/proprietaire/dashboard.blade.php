<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TouriStay - Proprietaire Dashboard</title>
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
                <h1 class="text-3xl font-bold tracking-wider">TouriStay Proprietaire</h1>
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
                <p class="text-touristay-white opacity-80">You’re a Proprietaire on TouriStay. Manage your listings and bookings below!</p>
            </div>

            <!-- Add Listing Form -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Add a New Listing 🏡</h2>
                <form action="{{ route('annonces.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="location" class="block text-lg">Location</label>
                            <input id="location" name="location" value="{{ old('location') }}" required class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                            @error('location') <span class="text-touristay-red">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="price" class="block text-lg">Price (per night)</label>
                            <input id="price" name="price" type="number" step="0.01" value="{{ old('price') }}" required class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                            @error('price') <span class="text-touristay-red">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="type_de_logement_id" class="block text-lg">Type</label>
                            <select id="type_de_logement_id" name="type_de_logement_id" required class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}" {{ old('type_de_logement_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('type_de_logement_id') <span class="text-touristay-red">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="image" class="block text-lg">Image</label>
                            <input id="image" name="image" type="file" accept="image/*" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                            @error('image') <span class="text-touristay-red">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="available_until" class="block text-lg">Available Until</label>
                        <input id="available_until" name="available_until" type="date" value="{{ old('available_until') }}" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green" required>
                        @error('available_until') <span class="text-touristay-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-lg">Equipment</label>
                        @foreach ($equipements as $equipement)
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" name="equipements[]" value="{{ $equipement->id }}" {{ in_array($equipement->id, old('equipements', [])) ? 'checked' : '' }} class="mr-2">
                                {{ $equipement->name }}
                            </label>
                        @endforeach
                        @error('equipements') <span class="text-touristay-red">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                        Publish Listing
                    </button>
                </form>
            </section>

            <!-- Your Listings Section -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Your Listings 📋</h2>
                @if ($annonces->isEmpty())
                    <p class="text-touristay-white opacity-80">You haven’t created any listings yet. Add one above!</p>
                @else
                    <div class="space-y-4">
                        @foreach ($annonces as $annonce)
                            <div class="flex justify-between items-center bg-gray-800 p-4 rounded-lg">
                                <div class="flex items-center space-x-4">
                                    @if ($annonce->image)
                                        <img src="{{ Storage::url($annonce->image) }}" alt="{{ $annonce->location }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-600 rounded-lg flex items-center justify-center text-sm">No Image</div>
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-medium">{{ $annonce->location }}</h3>
                                        <p class="text-sm opacity-80">
                                            Type: {{ $annonce->typeDeLogement->name }} | 
                                            Price: ${{ $annonce->price }}/night | 
                                            Available Until: {{ $annonce->available_until ? $annonce->available_until->format('Y-m-d') : 'N/A' }} | 
                                            Equipment: {{ $annonce->equipements->pluck('name')->join(', ') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="space-x-2">
                                    <a href="{{ route('annonces.edit', $annonce->id) }}" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                                        Edit
                                    </a>
                                    <form action="{{ route('annonces.destroy', $annonce->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-touristay-red hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- Bookings Section -->
            <section class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Bookings for Your Listings 📅</h2>
                @if ($bookings->isEmpty())
                    <p class="text-touristay-white opacity-80">Your listings haven’t received any bookings yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-700">
                                    <th class="p-3">Tourist Name</th>
                                    <th class="p-3">Listing</th>
                                    <th class="p-3">Start Date</th>
                                    <th class="p-3">End Date</th>
                                    <th class="p-3">Total Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr class="border-b border-gray-600">
                                        <td class="p-3">{{ $booking->user->name }}</td>
                                        <td class="p-3">{{ $booking->annonce->location }}</td>
                                        <td class="p-3">{{ $booking->start_date->format('Y-m-d') }}</td>
                                        <td class="p-3">{{ $booking->end_date->format('Y-m-d') }}</td>
                                        <td class="p-3">${{ $booking->total_price }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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