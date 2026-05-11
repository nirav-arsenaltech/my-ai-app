@extends('layouts.public_note')

@section('title', 'Password Required')

@section('content')
    <div class="public-note-card text-center">
        <div class="mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <h1 class="public-note-title mb-2">Password Required</h1>
        <p class="text-gray-600 mb-8">This note is password protected. Please enter the password to view its content.</p>

        <form action="{{ route('notes.shared.verify', $note->share_token) }}" method="POST" class="password-form">
            @csrf
            <div class="form-group mb-4 text-left">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter password..." required autofocus>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary btn-block w-100">Unlock Note</button>
        </form>
    </div>
@endsection
