@extends('layouts.admin')

@section('title', $note->title)

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="breadcrumb-separator">
        <polyline points="9 18 15 12 9 6" />
    </svg>
    <a href="{{ route('notes.index') }}">Notes</a>
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="breadcrumb-separator">
        <polyline points="9 18 15 12 9 6" />
    </svg>
    <span class="breadcrumb-active">View Note</span>
@endsection

@section('page-heading-actions')
    <div class="flex gap-2">
        <a href="{{ route('notes.edit', $note) }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
            </svg>
            <span>Edit Note</span>
        </a>
        <button type="button" onclick="confirmDelete('{{ $note->id }}', '{{ addslashes($note->title) }}')"
            class="btn btn-outline-danger">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6" />
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                <path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2" />
            </svg>
            <span>Delete</span>
        </button>
    </div>
@endsection

@section('page-title', $note->title)
@section('page-subtitle')
    Created {{ $note->created_at->format('M d, Y') }} · Last updated {{ $note->updated_at->diffForHumans() }}
@endsection

@section('content')
    <div class="note-view-container">
        <div class="note-main-content">
            <div class="admin-card note-body-card">
                <div class="card-body">
                    <div class="note-text-display">{{ $note->content }}</div>
                </div>
            </div>
        </div>

        <div class="note-sidebar">
            <div class="admin-card share-status-card {{ $note->share_token ? 'is-shared' : '' }}">
                <div class="card-header">
                    <h2 class="card-title">Sharing Center</h2>
                </div>
                <div class="card-body">
                    @if ($note->share_token)
                        <div class="active-share-info">
                            <div class="share-badge-large">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                                </svg>
                                <span>Public Link Active</span>
                            </div>

                            <div class="share-link-box">
                                <input type="text" readonly value="{{ route('notes.shared.show', $note->share_token) }}"
                                    id="shareLinkInput">
                                <button onclick="copyShareLink()">Copy</button>
                            </div>

                            <div class="text-xs text-gray-500 mb-2 uppercase font-bold tracking-wider">Social Share</div>
                            <div class="social-share-grid mb-6">
                                <a href="https://wa.me/?text={{ urlencode($note->title . ': ' . route('notes.shared.show', $note->share_token)) }}"
                                    target="_blank" class="social-btn whatsapp" title="Share via WhatsApp">
                                    {{-- whatsapp svg --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                        <path
                                            d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
                                    </svg>
                                </a>
                                <a href="https://t.me/share/url?url={{ urlencode(route('notes.shared.show', $note->share_token)) }}&text={{ urlencode($note->title) }}"
                                    target="_blank" class="social-btn telegram" title="Share via Telegram">

                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-telegram" viewBox="0 0 16 16">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09" />
                                    </svg>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('notes.shared.show', $note->share_token)) }}"
                                    target="_blank" class="social-btn facebook" title="Share via Facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                                        <path
                                            d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
                                    </svg>
                                </a>
                                {{-- twitter --}}
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('notes.shared.show', $note->share_token)) }}&text={{ urlencode($note->title) }}"
                                    target="_blank" class="social-btn twitter" title="Share via Twitter">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-twitter-x" viewBox="0 0 16 16">
                                        <path
                                            d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" />
                                    </svg>
                                </a>
                                {{-- linked in --}}
                                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('notes.shared.show', $note->share_token)) }}"
                                    target="_blank" class="social-btn linkedin">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-linkedin" viewBox="0 0 16 16">
                                        <path
                                            d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z" />
                                    </svg>
                                </a>

                            </div>

                            <div class="share-details">
                                <div class="detail-item">
                                    <span class="detail-label">Status</span>
                                    <span class="detail-value text-success">Active</span>
                                </div>
                                @if ($note->expires_at)
                                    <div class="detail-item">
                                        <span class="detail-label">Expires</span>
                                        <span class="detail-value {{ $note->isExpired() ? 'text-danger' : '' }}">
                                            {{ $note->expires_at->format('M d, Y H:i') }}
                                        </span>
                                    </div>
                                @endif
                                <div class="detail-item">
                                    <span class="detail-label">Protection</span>
                                    <span class="detail-value">{{ $note->hasPassword() ? 'Password Set' : 'None' }}</span>
                                </div>
                            </div>

                            <div class="share-actions mt-6">
                                <button onclick="document.getElementById('share-modal').classList.remove('hidden')"
                                    class="btn btn-outline-primary w-full mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                                        <circle cx="12" cy="12" r="3" />
                                        <path
                                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                                    </svg>
                                    Update Settings
                                </button>
                                <button type="button" onclick="confirmRevokeShare()" class="btn btn-outline-danger w-full">
                                    Revoke Public Access
                                </button>
                                <form id="revoke-share-form" action="{{ route('notes.revoke-share', $note) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="no-share-state">
                            <div class="no-share-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </div>
                            <p>This note is currently <strong>Private</strong>. Only you can see it.</p>
                            <button onclick="document.getElementById('share-modal').classList.remove('hidden')"
                                class="btn btn-primary w-full mt-4">Generate Share Link</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Revoke Share Confirmation Modal --}}
    <div id="revoke-share-modal" class="confirm-modal" style="display:none">
        <div class="confirm-modal-backdrop" onclick="closeRevokeModal()"></div>
        <div class="confirm-modal-box">
            <h3 class="confirm-modal-title">Revoke Public Access?</h3>
            <p class="confirm-modal-body">
                This will immediately disable the public link. Anyone with the link or password will no longer be able to view this note.
            </p>
            <div class="confirm-modal-actions">
                <button type="button" class="btn btn-ghost" onclick="closeRevokeModal()">
                    Keep Active
                </button>
                <button type="button" class="btn btn-danger-solid" onclick="document.getElementById('revoke-share-form').submit()">
                    Yes, Revoke Access
                </button>
            </div>
        </div>
    </div>

    {{-- Global Delete Modal --}}
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

    {{-- Share Modal --}}
    <div id="share-modal" class="modal-overlay hidden">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Share Settings</h3>
                <button onclick="this.closest('.modal-overlay').classList.add('hidden')" class="modal-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('notes.share', $note) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-sm text-gray-500 mb-8">Create a public, read-only link for this note. You can optionally
                        set an expiration date and password.</p>

                    <div class="form-group mb-6">
                        <label class="premium-label mb-2">Expiration Date (Optional)</label>
                        <div class="premium-input-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="input-icon">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                            <input type="datetime-local" name="expires_at" id="expires_at_picker"
                                class="premium-control"
                                value="{{ $note->expires_at ? $note->expires_at->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        @error('expires_at')
                            <p class="text-danger text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                        <p class="form-text mt-2">Link will become inaccessible after this time.</p>
                    </div>

                    <div class="form-group mb-4">
                        <label class="premium-label mb-2">Password Protection (Optional)</label>
                        <div class="premium-input-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="input-icon">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <input type="password" name="password" class="premium-control"
                                placeholder="{{ $note->password ? '•••••••• (Keep empty to keep current)' : 'Set a secure password...' }}">
                        </div>
                        @error('password')
                            <p class="text-danger text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                        <p class="form-text mt-2">Visitors must enter this password to view the note.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="this.closest('.modal-overlay').classList.add('hidden')"
                        class="btn btn-secondary">Cancel</button>
                    <button type="submit"
                        class="btn btn-primary">{{ $note->share_token ? 'Save Changes' : 'Enable Sharing' }}</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .note-view-container {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .note-view-container {
                grid-template-columns: 1fr;
            }
        }

        .content-heading {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
        }

        .note-body-card {
            min-height: 500px;
            border-radius: 24px;
        }

        .note-text-display {
            font-size: 1.15rem;
            line-height: 1.8;
            color: var(--text);
            white-space: pre-wrap;
            font-family: 'Figtree', sans-serif;
        }

        .share-badge-large {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: var(--success-light);
            color: var(--success);
            border-radius: 12px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .share-link-box {
            display: flex;
            background: #f8fafc;
            border: 1px solid var(--card-border);
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 20px;
        }

        .share-link-box input {
            background: transparent;
            border: none;
            padding: 8px 12px;
            font-size: 0.85rem;
            color: var(--text-muted);
            flex: 1;
            min-width: 0;
        }

        .share-link-box button {
            background: white;
            border: 1px solid var(--card-border);
            padding: 0 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .share-link-box button:hover {
            background: var(--page-bg);
        }

        .social-share-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }

        .social-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 40px;
            border-radius: 10px;
            color: white;
            transition: all 0.2s;
        }

        .social-btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .social-btn.whatsapp {
            background: #25D366;
        }

        .social-btn.telegram {
            background: #0088cc;
        }

        .social-btn.facebook {
            background: #1877F2;
        }

        .social-btn.twitter {
            background: #000000;
        }

        .social-btn.linkedin {
            background: #0077B5;
        }

        .share-details {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }

        .detail-label {
            color: var(--text-subtle);
        }

        .detail-value {
            font-weight: 600;
            color: var(--text);
        }

        .no-share-state {
            text-align: center;
            padding: 20px 0;
        }

        .no-share-icon {
            width: 60px;
            height: 60px;
            background: var(--page-bg);
            color: var(--text-subtle);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .no-share-state p {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-container {
            background: white;
            border-radius: 20px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: modal-pop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modal-pop {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text);
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-subtle);
            cursor: pointer;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            padding: 16px 24px;
            background: #f8fafc;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .hidden {
            display: none;
        }

        /* Premium Input Styles */
        .premium-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: 0.05em;
        }

        .premium-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: var(--text-subtle);
        }

        .premium-control {
            width: 100%;
            padding: 14px 14px 14px 44px;
            background: #f1f5f9;
            border: 2px solid transparent;
            border-radius: 14px;
            font-size: 0.95rem;
            color: var(--text);
            transition: all 0.2s;
            outline: none;
        }

        .premium-control:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px var(--primary-ring);
        }
    </style>

    <script>
        function copyShareLink() {
            const input = document.getElementById('shareLinkInput');
            const text = input.value;

            const btn = event.target;
            const originalText = btn.innerText;

            const copyAction = (success) => {
                if (success) {
                    btn.innerText = 'Copied!';
                    btn.style.borderColor = 'var(--success)';
                    btn.style.color = 'var(--success)';

                    setTimeout(() => {
                        btn.innerText = originalText;
                        btn.style.borderColor = '';
                        btn.style.color = '';
                    }, 2000);
                }
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => copyAction(true));
            } else {
                // Fallback for non-secure contexts
                input.select();
                try {
                    const successful = document.execCommand('copy');
                    copyAction(successful);
                } catch (err) {
                    console.error('Fallback copy failed', err);
                }
            }
        }

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

        function confirmRevokeShare() {
            document.getElementById('revoke-share-modal').style.display = 'flex';
        }

        function closeRevokeModal() {
            document.getElementById('revoke-share-modal').style.display = 'none';
        }

        // Prevent past dates in share settings
        document.addEventListener('DOMContentLoaded', function() {
            const datePicker = document.getElementById('expires_at_picker');
            if (datePicker) {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');

                const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
                datePicker.min = minDateTime;
            }

            // Keep modal open if there are errors
            @if ($errors->has('expires_at') || $errors->has('password'))
                document.getElementById('share-modal').classList.remove('hidden');
            @endif
        });
    </script>
@endsection
