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

    // Fetch all bookings for this listing between now and available_until
    $bookings = Booking::where('annonce_id', $id)
        ->where('end_date', '>=', now()->toDateString())
        ->where('start_date', '<=', $listing->available_until)
        ->get();

    // Generate an array of booked dates
    $bookedDates = [];
    foreach ($bookings as $booking) {
        $start = \Carbon\Carbon::parse($booking->start_date);
        $end = \Carbon\Carbon::parse($booking->end_date);

        // Include all dates in the range from start_date to end_date
        while ($start->lte($end)) {
            $bookedDates[] = $start->toDateString(); // e.g., "2025-03-01"
            $start->addDay();
        }
    }

    // Remove duplicates and sort
    $bookedDates = array_unique($bookedDates);
    sort($bookedDates);

    return view('tourist.book', compact('listing', 'bookedDates'));
}

public function storeBooking(Request $request)
{
    $request->validate([
        'annonce_id' => 'required|exists:annonces,id',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'required|date|after:start_date',
    ]);

    $listing = Annonce::findOrFail($request->annonce_id);

    if ($request->end_date > $listing->available_until) {
        return redirect()->back()->withErrors(['end_date' => 'End date cannot be after the listing\'s available until date.']);
    }

    // Check for overlapping bookings
    $existingBookings = Booking::where('annonce_id', $request->annonce_id)
        ->where(function ($query) use ($request) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function ($q) use ($request) {
                      $q->where('start_date', '<=', $request->start_date)
                        ->where('end_date', '>=', $request->end_date);
                  });
        })
        ->exists();

    if ($existingBookings) {
        return redirect()->back()->withErrors(['start_date' => 'The selected dates overlap with an existing booking.']);
    }

    $start = \Carbon\Carbon::parse($request->start_date);
    $end = \Carbon\Carbon::parse($request->end_date);
    $nights = $start->diffInDays($end, true);
    if ($start->gt($end)) {
        return redirect()->back()->withErrors(['end_date' => 'End date must be after start date.']);
    }

    $totalPrice = $listing->price * $nights;

    $booking = Booking::create([
        'user_id' => Auth::id(),
        'annonce_id' => $request->annonce_id,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'total_price' => $totalPrice,
    ]);

    // Notify the proprietaire
    $proprietaire = $listing->user;
    $proprietaire->notify(new \App\Notifications\BookingCreatedNotification($booking));

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