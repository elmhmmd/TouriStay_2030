<?php
namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Booking;
use App\Notifications\BookingCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use App\Services\InvoiceGenerator;

class TouristController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('bookings', 'bookings.annonce', 'bookings.annonce.typeDeLogement', 'bookings.annonce.equipements');
        return view('tourist.dashboard', compact('user'));
    }

    public function downloadInvoice($bookingId)
{
    $booking = Booking::where('user_id', Auth::id())
        ->findOrFail($bookingId);

    $invoiceGenerator = new InvoiceGenerator();
    return $invoiceGenerator->generate($booking);
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

public function payment($bookingId)
{
    $booking = Booking::where('user_id', Auth::id())->findOrFail($bookingId);
    $listing = $booking->annonce;

    return view('tourist.payment', compact('booking', 'listing'));
}

public function processPayment(Request $request, $bookingId)
{
    $booking = Booking::where('user_id', Auth::id())->findOrFail($bookingId);
    $orderId = $request->input('order_id');

    // Set up PayPal client
    $clientId = config('paypal.sandbox.client_id');
    $clientSecret = config('paypal.sandbox.client_secret');
    $environment = new SandboxEnvironment($clientId, $clientSecret);
    $client = new PayPalHttpClient($environment);

    // Retrieve the order details
    $request = new OrdersGetRequest($orderId);
    try {
        $response = $client->execute($request);
        $order = $response->result;

        if ($order->status === 'COMPLETED') {

            $booking->update(['status' => 'confirmed']);
            // Notify the proprietaire
            $proprietaire = $booking->annonce->user;
            $notification = new \App\Notifications\BookingCreatedNotification($booking);
            $proprietaire->notify($notification);

            // Manually broadcast the notification synchronously
            \Illuminate\Support\Facades\Broadcast::event(new \Illuminate\Notifications\Events\BroadcastNotificationCreated($notification, $proprietaire));

            return redirect()->route('tourist.dashboard')->with('success', 'Payment successful and booking confirmed!');
        } else {
            return redirect()->route('tourist.payment', $booking->id)
                ->withErrors(['payment' => 'Payment not completed.']);
        }
    } catch (\Exception $e) {
        return redirect()->route('tourist.payment', $booking->id)
            ->withErrors(['payment' => 'Payment failed: ' . $e->getMessage()]);
    }
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
        ->where('status', 'confirmed')
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

    // Calculate total price
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

    // Redirect to payment page
    return redirect()->route('tourist.payment', $booking->id);
}

}