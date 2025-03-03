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
        return view('dashboards.admin', compact('annonces', 'equipements', 'types'));
    }

    return view('dashboards.proprietaire', compact('annonces', 'equipements', 'types', 'bookings', 'notifications', 'unreadNotifications'));
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

    public function destroy($id)
    {
        $annonce = Annonce::where('user_id', Auth::id())->findOrFail($id);
        if ($annonce->image) {
            Storage::disk('public')->delete($annonce->image);
        }
        $annonce->delete();
        return redirect()->back()->with('success', 'Listing deleted successfully.');
    }
}