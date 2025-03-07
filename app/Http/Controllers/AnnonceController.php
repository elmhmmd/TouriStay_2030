<?php
namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Equipement;
use App\Models\TypeDeLogement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnonceController extends Controller
{
   
public function index()
{
    $annonces = Annonce::where('user_id', Auth::id())
        ->with(['typeDeLogement', 'equipements'])
        ->get();

    // Fetch bookings for the proprietaire's listings
    $bookings = Annonce::where('user_id', Auth::id())
        ->with(['bookings.user'])
        ->get()
        ->flatMap->bookings;

    // Fetch notifications for the proprietaire
    $user = Auth::user();
    $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
    $unreadNotifications = $user->unreadNotifications()->orderBy('created_at', 'desc')->get();

    $equipements = Equipement::all();
    $types = TypeDeLogement::all();

    if (Auth::user()->role->name === 'admin') {
        // Admin-specific data
        $totalUsers = \App\Models\User::count();
        $totalBookings = \App\Models\Booking::count();
        $activeListings = \App\Models\Annonce::where('available_until', '>=', now()->toDateString())->count();
        $allListings = \App\Models\Annonce::with(['user', 'typeDeLogement'])->get();
        $recentBookings = \App\Models\Booking::with(['user', 'annonce'])->orderBy('created_at', 'desc')->take(10)->get();

        return view('admin.dashboard', compact('annonces', 'equipements', 'types', 'totalUsers', 'totalBookings', 'activeListings', 'allListings', 'recentBookings'));
    }

    return view('proprietaire.dashboard', compact('annonces', 'equipements', 'types', 'bookings', 'notifications', 'unreadNotifications'));
}

    public function markNotificationAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'type_de_logement_id' => 'required|exists:type_de_logements,id',
            'available_until' => 'required|date|after_or_equal:today',
            'equipements' => 'required|array',
            'equipements.*' => 'exists:equipements,id',
            'image' => 'nullable|image',
        ]);

       

        $data = $request->only(['location', 'price', 'type_de_logement_id', 'available_until']);
        $data['user_id'] = Auth::id();

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('annonces', 'public');
            $data['image'] = $path;
        }

        $annonce = Annonce::create($data);
        $annonce->equipements()->sync($request->equipements);

        return redirect()->back()->with('success', 'Listing created successfully.');
    }

    public function edit($id)
    {
        $annonce = Annonce::where('user_id', Auth::id())->with(['typeDeLogement', 'equipements'])->findOrFail($id);
        $equipements = Equipement::all();
        $types = TypeDeLogement::all();
        return view('proprietaire.annonce_edit', compact('annonce', 'equipements', 'types'));
    } 

    public function update(Request $request, $id)
    {
        $annonce = Annonce::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'type_de_logement_id' => 'required|exists:type_de_logements,id',
            'available_until' => 'required|date|after_or_equal:today',
            'equipements' => 'required|array',
            'equipements.*' => 'exists:equipements,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['location', 'price', 'type_de_logement_id', 'available_until']);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($annonce->image) {
                Storage::disk('public')->delete($annonce->image);
            }
            $path = $request->file('image')->store('annonces', 'public');
            $data['image'] = $path;
        }

        $annonce->update($data);
        $annonce->equipements()->sync($request->equipements);

        return redirect()->route('proprietaire.dashboard')->with('success', 'Listing updated successfully.');
    }

   // app/Http/Controllers/AnnonceController.php
public function destroy($id)
{
    $annonce = Auth::user()->role->name === 'admin'
        ? Annonce::findOrFail($id) // Admin can delete any listing
        : Annonce::where('user_id', Auth::id())->findOrFail($id); // Proprietaire restricted to own listings

    if ($annonce->image) {
        Storage::disk('public')->delete($annonce->image);
    }
    $annonce->delete();

    return redirect()->route('admin.dashboard')->with('success', 'Listing deleted successfully.');
}
}