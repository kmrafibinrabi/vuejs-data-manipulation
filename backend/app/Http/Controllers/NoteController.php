<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Note::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string',
            'color' => 'nullable|string'
        ]);

        $note = Note::create($validated);
        return response()->json($note, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        return response()->json($note);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string',
            'color' => 'nullable|string'
        ]);

        $note->update($validated);
        return response()->json($note);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json(null, 204);
    }

    /**
     * Search notes by title or content
     */
    public function search(Request $request)
    {
        $query = $request->input('query', '');

        $notes = Note::where('title', 'LIKE', "%$query%")
            ->orWhere('content', 'LIKE', "%$query%")
            ->get();

        return response()->json($notes);
    }
}

