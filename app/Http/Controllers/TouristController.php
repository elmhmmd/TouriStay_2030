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
    $user = auth()->user()->load('bookings.annonce.typeDeLogement', 'bookings.annonce.equipements');
    return view('dashboards.tourist', compact('user'));
}

    public function listings(Request $request)
    {
        $query = Annonce::query()
            ->with(['typeDeLogement', 'equipements'])
            ->where('available_until', '>=', now()->toDateString());

        // Apply filters
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        if ($request->filled('type_de_logement_id')) {
            $query->where('type_de_logement_id', $request->input('type_de_logement_id'));
        }

        $listings = $query->get();
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

        if ($request->end_date > $listing->available_until) {
            return redirect()->back()->withErrors(['end_date' => 'End date cannot be after the listing\'s available until date.']);
        }

        $start = \Carbon\Carbon::parse($request->start_date);
        $end = \Carbon\Carbon::parse($request->end_date);
        $nights = $end->diffInDays($start);
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