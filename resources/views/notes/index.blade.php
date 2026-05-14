@extends('layouts.admin')

@section('title', 'My Notes')

@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
    <polyline points="9,18 15,12 9,6" /></svg>
<span class="breadcrumb-active">Notes</span>
@endsection


{{-- Removed New Note from topbar as requested --}}

@section('page-title', 'Personal Notes')
@section('page-subtitle', 'Your private space for thoughts, ideas, and shared knowledge.')
@section('page-heading-actions')
<a href="{{ route('notes.create') }}" class="btn btn-primary">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    <span>New Note</span>
</a>
@endsection

@section('content')
    <div class="admin-card">
        <div class="card-header">
            <div class="card-header-info">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" height="17" width="17">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    Filter Notes
                </h2>
                <span class="header-status">Search by note title</span>
            </div>
        </div>

        <div class="card-body">
            <form method="GET" class="admin-filter-form">
                <input type="hidden" name="per_page" value="{{ request('per_page', $notes->perPage()) }}">
                <div class="form-group">
                    <label class="form-label" for="search">Search</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search notes by note title">
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-input">
                        <option value="" @selected(request('status') === '')>All</option>
                        <option value="shared" @selected(request('status') === 'shared')>Shared</option>
                        <option value="private" @selected(request('status') === 'private')>Private</option>
                    </select>
                </div>

                <div class="admin-filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('notes.index') }}" class="btn btn-ghost">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="notes-grid">
        @forelse($notes as $note)
            <div class="note-card-wrapper">
                <div class="note-card">
                    <div class="note-card-header">
                        <div class="note-status">
                            @if($note->share_token)
                                <span class="status-badge status-success" title="Shared">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                                    Shared
                                </span>
                                @if($note->isExpired())
                                    <span class="status-badge status-danger">Expired</span>
                                @else
                                    <span class="status-badge status-active">Active</span>
                                @endif
                                @if($note->hasPassword())
                                    <span class="status-badge status-purple" title="Password Protected">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    </span>
                                @endif
                            @else
                                <span class="status-badge status-info" title="Private">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" width="10" height="10" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
                                        Private
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('notes.show', $note) }}" class="note-action-btn" title="View Note">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <div class="note-actions-dropdown" x-data="{ open: false, shareMenu: false }">
                                <button @click="open = !open; shareMenu = false" class="note-action-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                                </button>
                                
                                {{-- Main Dropdown --}}
                                <div x-show="open" @click.away="open = false" class="note-dropdown-menu" style="display: none;">
                                    <a href="{{ route('notes.edit', $note) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        Edit Note
                                    </a>

                                    <button type="button" onclick="downloadNote('{{ $note->id }}', '{{ addslashes($note->title) }}', event)" class="download-btn">
                                        <span class="icon-container mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        </span>
                                        Download (.txt)
                                    </button>
                                    
                                    @if($note->share_token && !$note->isExpired())
                                        <button @click="shareMenu = !shareMenu">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                                            Share Options
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline ml-auto"><polyline points="9 18 15 12 9 6"/></svg>
                                        </button>
                                        
                                        {{-- Sub-menu for Sharing --}}
                                        <div x-show="shareMenu" class="share-submenu mt-1 ml-4 border-l-2 border-gray-100 pl-2">
                                            <button onclick="copyToClipboard('{{ route('notes.shared.show', $note->share_token) }}', event)" class="share-item">
                                                <span>Copy Link</span>
                                            </button>
                                            <a href="https://wa.me/?text={{ urlencode($note->title . ': ' . route('notes.shared.show', $note->share_token)) }}" target="_blank" class="share-item">WhatsApp</a>
                                            <a href="https://t.me/share/url?url={{ urlencode(route('notes.shared.show', $note->share_token)) }}&text={{ urlencode($note->title) }}" target="_blank" class="share-item">Telegram</a>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('notes.shared.show', $note->share_token)) }}" target="_blank" class="share-item">Facebook</a>
                                            <a href="https://twitter.com/share?url={{ urlencode(route('notes.shared.show', $note->share_token)) }}&text={{ urlencode($note->title) }}" target="_blank" class="share-item">Twitter</a>
                                            <a href="https://www.linkedin.com/shareArticle?url={{ urlencode(route('notes.shared.show', $note->share_token)) }}&title={{ urlencode($note->title) }}" target="_blank" class="share-item">LinkedIn</a>
                                        </div>
                                    @else
                                        <a href="{{ route('notes.show', $note) }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                            Enable Sharing
                                        </a>
                                    @endif
    
                                    <div class="dropdown-divider"></div>
                                    <button type="button" @click="confirmDelete('{{ $note->id }}', '{{ addslashes($note->title) }}')" class="text-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        Delete Note
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="note-card-body">
                        <a href="{{ route('notes.show', $note) }}" class="note-title">{{ $note->title }}</a>
                        <p class="note-snippet mt-3" title="{{ $note->content }}">{{ Str::limit($note->content, 120) }}</p>
                    </div>
                    
                    <div class="note-card-footer">
                        <span class="note-date">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $note->created_at->format('M d, Y') }}
                        </span>
                        <div class="note-author">
                            <div class="author-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state-card col-span-full">
                <div class="empty-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14.5 2 14.5 7.5 20 7.5"/></svg>
                </div>
                <h3>No notes yet</h3>
                <p>Start organizing your thoughts and ideas today.</p>
                <a href="{{ route('notes.create') }}" class="btn btn-primary mt-4">Create Your First Note</a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $notes->links() }}
    </div>

    {{-- Global Delete Modal (matches app style) --}}
    <div id="note-delete-modal" class="confirm-modal" style="display:none">
        <div class="confirm-modal-backdrop" onclick="closeDeleteModal()"></div>
        <div class="confirm-modal-box">
            <h3 class="confirm-modal-title">Delete Note?</h3>
            <p class="confirm-modal-body" id="delete-modal-text">
                This will permanently delete the note. This action cannot be undone.
            </p>
            <div class="confirm-modal-actions">
                <button type="button" class="btn btn-ghost" onclick="closeDeleteModal()">
                    Cancel
                </button>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger-solid">
                        Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .notes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }
        .note-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 24px;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            /* overflow: hidden; */ {{-- Removed to allow dropdowns to overflow --}}
            box-shadow: var(--card-shadow);
            z-index: 1;
        }
        .note-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.1);
            z-index: 10; {{-- Bring hovered card to front --}}
        }
        .note-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            position: relative;
            z-index: 20;
        }
        .note-status {
            display: flex;
            gap: 6px;
        }
        .note-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 12px;
            line-height: 1.3;
        }
        .note-snippet {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 24px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .note-card-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        .note-date {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-subtle);
            font-size: 0.85rem;
            font-weight: 500;
        }
        .author-avatar {
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, var(--primary), var(--purple));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
        }
        .note-action-btn {
            background: none;
            border: none;
            color: var(--text-subtle);
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .note-action-btn:hover {
            background: rgba(0,0,0,0.05);
            color: var(--text);
        }
        .note-dropdown-menu {
            position: absolute;
            right: 5px;
            top: 50px;
            background: white;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            z-index: 100;
            min-width: 200px;
            overflow: visible;
            padding: 6px;
        }
        .note-dropdown-menu a, .note-dropdown-menu button {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 10px 14px;
            font-size: 0.9rem;
            color: var(--text);
            text-align: left;
            border-radius: 8px;
            transition: all 0.2s;
            background: none;
            border: none;
        }
        .note-dropdown-menu a:hover, .note-dropdown-menu button:hover {
            background: var(--page-bg);
        }
        .icon-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 14px;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        .share-item {
            padding: 6px 12px !important;
            font-size: 0.8rem !important;
            color: var(--text-muted) !important;
        }
        .share-item:hover {
            color: var(--primary) !important;
        }
        .dropdown-divider {
            height: 1px;
            background: rgba(0,0,0,0.05);
            margin: 4px 0;
        }
        .empty-state-card {
            background: white;
            border: 2px dashed var(--card-border);
            border-radius: 24px;
            padding: 60px 40px;
            text-align: center;
        }
        .empty-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        .status-badge {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-success { background: #dcfce7; color: #166534; }
        .status-danger { background: #fee2e2; color: #991b1b; }
        .status-purple { background: #f3e8ff; color: #6b21a8; }
        .status-info { background: #dcf4fc; color: #0f5a80; }
        .status-active { background: #e0f2fe; color: #0284c7; }
    </style>

    <script>
        function confirmDelete(id, title) {
            const modal = document.getElementById('note-delete-modal');
            const form = document.getElementById('delete-form');
            const text = document.getElementById('delete-modal-text');
            
            form.action = `/notes/${id}`;
            text.innerHTML = `Are you sure you want to delete <strong>"${title}"</strong>? This action cannot be undone.`;
            modal.style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('note-delete-modal').style.display = 'none';
        }

        function copyToClipboard(text, event) {
            const btn = event.currentTarget;
            const span = btn.querySelector('span');
            const originalText = span.innerText;

            const copyAction = (success) => {
                if (success) {
                    span.innerText = 'Copied!';
                    btn.classList.add('text-success');
                    
                    if (window.createToast) {
                        window.createToast({
                            type: 'success',
                            title: 'Link Copied',
                            message: 'Share link has been copied to clipboard.'
                        });
                    }

                    setTimeout(() => {
                        span.innerText = originalText;
                        btn.classList.remove('text-success');
                    }, 2000);
                }
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => copyAction(true));
            } else {
                // Fallback for non-secure contexts
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-9999px";
                textArea.style.top = "0";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    const successful = document.execCommand('copy');
                    copyAction(successful);
                } catch (err) {
                    console.error('Fallback copy failed', err);
                }
                document.body.removeChild(textArea);
            }
        }

        async function downloadNote(id, title, event) {
            const btn = event.currentTarget;
            if (btn.disabled) return;

            const iconContainer = btn.querySelector('.icon-container');
            const originalIcon = iconContainer.innerHTML;
            
            // Disable button and show loader
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.style.cursor = 'not-allowed';
            iconContainer.innerHTML = '<svg class="animate-spin inline" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56" /></svg>';

            try {
                const response = await fetch(`/notes/${id}/download`);
                if (!response.ok) throw new Error('Download failed');

                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `${title.replace(/[^a-z0-9]/gi, '_').toLowerCase()}.txt`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                if (window.createToast) {
                    window.createToast({
                        type: 'success',
                        title: 'Download Successful',
                        message: `"${title}" has been downloaded.`
                    });
                }
            } catch (error) {
                console.error(error);
                if (window.createToast) {
                    window.createToast({
                        type: 'error',
                        title: 'Download Failed',
                        message: 'An error occurred while downloading the note.'
                    });
                }
            } finally {
                // Restore button state
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
                iconContainer.innerHTML = originalIcon;
            }
        }
    </script>
@endsection
