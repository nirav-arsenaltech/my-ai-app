<?php

namespace App\Http\Controllers;

use App\Http\Requests\NoteRequest;
use App\Models\Note;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NoteController extends Controller
{
    public function __construct(protected OpenAIService $ai) {}

    public function index()
    {
        $notes = auth()->user()->notes()->latest()->paginate(10);

        return view('notes.index', compact('notes'));
    }

    public function create()
    {
        return view('notes.create');
    }

    public function store(NoteRequest $request)
    {
        // if ($request->user()->notes()->count() >= Note::MAX_NOTES_PER_USER) {
        //     return redirect()->route('notes.index')
        //         ->with('error', 'You have reached the maximum number of notes allowed.');
        // }

        $request->user()->notes()->create($request->validated());

        return redirect()->route('notes.index')
            ->with('success', 'Note created successfully.');
    }

    public function show(Note $note)
    {
        $this->authorize('view', $note);

        return view('notes.show', compact('note'));
    }

    public function edit(Note $note)
    {
        $this->authorize('update', $note);

        return view('notes.edit', compact('note'));
    }

    public function update(NoteRequest $request, Note $note)
    {
        $this->authorize('update', $note);
        $note->update($request->validated());

        return redirect()->route('notes.index')
            ->with('success', 'Note updated successfully.');
    }

    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);
        $note->delete();

        return redirect()->route('notes.index')
            ->with('success', 'Note deleted successfully.');
    }

    public function share(Request $request, Note $note)
    {
        $this->authorize('update', $note);

        $validated = $request->validate([
            'expires_at' => ['nullable', 'date', 'after:now'],
            'password' => ['nullable', 'string', 'min:4'],
        ]);

        $data = [
            'share_token' => $note->share_token ?? Str::random(32),
            'expires_at' => $validated['expires_at'],
        ];

        // Only update password if a new one is provided.
        // If the user wants to remove it, they should revoke sharing and re-enable it,
        // or we could add a clear-password flag. For now, let's just update if provided.
        if (! empty($validated['password'])) {
            $data['password'] = $validated['password']; // Hashed by model cast
        }

        $note->update($data);

        return back()->with('success', 'Share settings updated.');
    }

    public function revokeShare(Note $note)
    {
        $this->authorize('update', $note);
        $note->update([
            'share_token' => null,
            'expires_at' => null,
            'password' => null,
        ]);

        return back()->with('success', 'Sharing revoked.');
    }

    public function fixGrammar(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $fixedText = $this->ai->fixGrammar($request->get('content'));

        return response()->json([
            'content' => $fixedText,
        ]);
    }
}
