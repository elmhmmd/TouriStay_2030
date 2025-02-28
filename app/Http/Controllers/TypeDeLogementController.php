<?php

namespace App\Http\Controllers;

use App\Models\TypeDeLogement;
use Illuminate\Http\Request;

class TypeDeLogementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'types' => 'required|string',
        ]);

        // Split the textarea input into an array of types (one per line)
        $types = array_filter(array_map('trim', explode("\n", $request->types)));

        foreach ($types as $typeName) {
            TypeDeLogement::create(['name' => $typeName]);
        }

        return redirect()->back()->with('type_success', count($types) . ' types of logements added successfully.');
    }

    public function destroy($id)
    {
        $type = TypeDeLogement::findOrFail($id);
        $type->delete();
        return redirect()->back()->with('type_success', 'Type of logement deleted successfully.');
    }
}
