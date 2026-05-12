@extends('layouts.public_note')

@section('title', $note->title)

@section('content')
    <div class="public-note-card">
        <header class="public-note-header">
            <h1 class="public-note-title">{{ $note->title }}</h1>
            <div class="public-note-meta">
                Last updated {{ $note->updated_at->diffForHumans() }}
            </div>
        </header>

        <div class="public-note-content">{{ $note->content }}</div>
    </div>
@endsection
