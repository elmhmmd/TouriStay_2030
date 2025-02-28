<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TouriStay - Edit Listing</title>
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
                <h1 class="text-3xl font-bold tracking-wider">TouriStay - Edit Listing</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('proprietaire.dashboard') }}" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
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
            <div class="bg-touristay-dark border border-touristay-green rounded-lg p-6 shadow-lg">
                <h2 class="text-2xl font-semibold mb-4">Edit Listing: {{ $annonce->location }}</h2>
                @if (session('success'))
                    <p class="text-touristay-green mb-4">{{ session('success') }}</p>
                @endif
                <form action="{{ route('annonces.update', $annonce->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="location" class="block text-lg">Location</label>
                            <input id="location" name="location" value="{{ old('location', $annonce->location) }}" required class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                            @error('location') <span class="text-touristay-red">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="price" class="block text-lg">Price (per night)</label>
                            <input id="price" name="price" type="number" step="0.01" value="{{ old('price', $annonce->price) }}" required class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                            @error('price') <span class="text-touristay-red">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="type_de_logement_id" class="block text-lg">Type</label>
                            <select id="type_de_logement_id" name="type_de_logement_id" required class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}" {{ old('type_de_logement_id', $annonce->type_de_logement_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('type_de_logement_id') <span class="text-touristay-red">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="image" class="block text-lg">Image</label>
                            @if ($annonce->image)
                                <img src="{{ Storage::url($annonce->image) }}" alt="Current Image" class="w-16 h-16 object-cover rounded-lg mb-2">
                            @endif
                            <input id="image" name="image" type="file" accept="image/*" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green">
                            @error('image') <span class="text-touristay-red">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="mb-4">
    <label for="available_until" class="block text-lg">Available Until</label>
    <input id="available_until" name="available_until" type="date" value="{{ old('available_until', $annonce->available_until) }}" class="w-full p-2 rounded-lg bg-gray-800 text-touristay-white border border-touristay-green" required>
    @error('available_until') <span class="text-touristay-red">{{ $message }}</span> @enderror
</div>
                    <div class="mb-4">
                        <label class="block text-lg">Equipment</label>
                        @foreach ($equipements as $equipement)
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" name="equipements[]" value="{{ $equipement->id }}" {{ $annonce->equipements->contains($equipement->id) ? 'checked' : '' }} class="mr-2">
                                {{ $equipement->name }}
                            </label>
                        @endforeach
                        @error('equipements') <span class="text-touristay-red">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="bg-touristay-green hover:bg-opacity-80 text-touristay-dark font-semibold px-4 py-2 rounded-lg transition duration-300">
                        Update Listing
                    </button>
                </form>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-touristay-red p-4 text-center">
            <p>© 2025 TouriStay. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>