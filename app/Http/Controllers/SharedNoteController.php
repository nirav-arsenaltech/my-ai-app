<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SharedNoteController extends Controller
{
    public function show(string $token)
    {
        $note = Note::where('share_token', $token)->firstOrFail();

        if ($note->isExpired()) {
            return view('notes.shared.expired');
        }

        if ($note->hasPassword() && ! session()->has("note_auth_{$note->id}")) {
            return view('notes.shared.password', compact('note'));
        }

        return view('notes.shared.view', compact('note'));
    }

    public function verifyPassword(Request $request, string $token)
    {
        $note = Note::where('share_token', $token)->firstOrFail();

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (Hash::check($request->password, $note->password)) {
            session()->put("note_auth_{$note->id}", true);

            return redirect()->route('notes.shared.show', $token);
        }

        return back()->withErrors(['password' => 'Incorrect password.']);
    }
}
