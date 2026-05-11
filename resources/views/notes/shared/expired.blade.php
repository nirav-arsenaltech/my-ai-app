@extends('layouts.public_note')

@section('title', 'Link Expired')

@section('content')
    <div class="public-note-card text-center">
        <div class="mb-6 text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <h1 class="public-note-title mb-2">Link Expired</h1>
        <p class="text-gray-600 mb-8">This shareable link has expired and is no longer available.</p>
        <a href="/" class="btn btn-secondary">Go to Homepage</a>
    </div>
@endsection
