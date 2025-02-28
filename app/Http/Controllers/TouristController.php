<?php
namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TouristController extends Controller
{
    public function index()
    {
        return view('toursist.dashboard');
    }

    public function listings(Request $request)
{
    $query = Annonce::query()
        ->with(['typeDeLogement', 'equipements'])
        ->where('available_until', '>=', now()->toDateString());

    // Apply fuzzy search across multiple fields
    if ($request->filled('search')) {
        $searchTerm = $request->input('search');

        $query->where(function ($q) use ($searchTerm) {
            $q->where('location', 'like', '%' . $searchTerm . '%')
              ->orWhere('price', 'like', '%' . $searchTerm . '%')
              ->orWhereHas('typeDeLogement', function ($q) use ($searchTerm) {
                  $q->where('name', 'like', '%' . $searchTerm . '%');
              });
        });
    }

    // Paginate results (10 per page)
    $listings = $query->paginate(10)->appends($request->only('search')); // Keep search term in pagination links
    $types = \App\Models\TypeDeLogement::all();

    return view('tourist.listings', compact('listings', 'types'));
}

    public function book($id)
    {
        $listing = Annonce::with(['typeDeLogement', 'equipements'])->findOrFail($id);
        return view('tourist.book', compact('listing'));
    }

    public function storeBooking(Request $request)
{
    $request->validate([
        'annonce_id' => 'required|exists:annonces,id',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after:start_date',
    ]);

    $listing = Annonce::findOrFail($request->annonce_id);

    // Check if end_date is before available_until
    if ($request->end_date > $listing->available_until) {
        return redirect()->back()->withErrors(['end_date' => 'End date cannot be after the listing\'s available until date.']);
    }

    // Calculate total price (price per night × number of nights)
    $start = \Carbon\Carbon::parse($request->start_date);
    $end = \Carbon\Carbon::parse($request->end_date);

    // Ensure the difference is always positive
    $nights = $start->diffInDays($end, true);
    if ($start->gt($end)) { // Additional safety check
        return redirect()->back()->withErrors(['end_date' => 'End date must be after start date.']);
    }

    $totalPrice = $listing->price * $nights;

    Booking::create([
        'user_id' => Auth::id(),
        'annonce_id' => $request->annonce_id,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'total_price' => $totalPrice,
    ]);

    return redirect()->route('tourist.dashboard')->with('success', 'Booking created successfully.');
}
    public function cancelBooking($id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        if (\Carbon\Carbon::parse($booking->start_date)->isPast()) {
            return redirect()->back()->withErrors(['error' => 'Cannot cancel a booking that has already started.']);
        }

        $booking->delete();
        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }
}