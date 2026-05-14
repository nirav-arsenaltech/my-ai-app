@extends('layouts.admin')

@section('title', 'Pages')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="9,18 15,12 9,6" />
    </svg>
    <span class="breadcrumb-active">Pages</span>
@endsection

@section('page-title', 'Pages')
@section('page-subtitle', 'Manage dynamic pages like Privacy Policy or Terms of Service.')

@section('page-heading-actions')
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19" />          
            <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Add Page
    </a>
@endsection

@section('content')


    <div class="admin-card">
        <div class="card-header">
            <div class="card-header-info">
                <h2 class="card-title">
                    {{-- pages svg --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
                    </svg>
                    Filter Pages
                </h2>
                <span class="header-status">Search by page title & status.</span>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="admin-filter-form">
                <input type="hidden" name="per_page" value="{{ request('per_page', $pages->perPage()) }}">
                <div class="form-group">
                    <label class="form-label" for="search">Search</label>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" class="form-input"
                           placeholder="Search pages by title">
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-input">
                        <option value="">All statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div class="admin-filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-ghost">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="card-header">
            <div class="card-header-info">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"></path>
                        <path d="M3 12h18"></path>
                        <path d="M3 18h18"></path>
                    </svg>
                    Pages Directory
                </h2>
                <span class="header-status">{{ $pages->total() }} total {{ Str::plural('page', $pages->total()) }}</span>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($pages->count())
                <div class="table-shell">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Page</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="table-actions-cell">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pages as $page)
                                <tr>
                                    <td>
                                        <div class="admin-user-cell">
                                            <div class="admin-user-avatar" style="{{ $page->is_active ? 'background: linear-gradient(135deg, #1d4ed8, #0f766e)' : 'background: #3a3f4b' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" height="16" width="16">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                </svg>
                                            </div>
                                            <div class="admin-user-meta">
                                                <div class="admin-user-name">{{ $page->title }}</div>
                                                <div class="admin-user-subtitle">ID: {{ $page->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($page->slug)
                                            <a href="{{ url('/p/' . $page->slug) }}" target="_blank" class="text-blue">/p/{{ $page->slug }}</a>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="role-badge {{ $page->is_active ? 'telegram-enabled' : 'telegram-disabled' }}">
                                            {{ $page->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="table-meta">
                                            <strong>{{ $page->created_at->format('M d, Y') }}</strong>
                                        </div>
                                    </td>
                                    <td class="table-actions-cell">
                                        <div class="table-actions">
                                            <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-outline-primary btn-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 20h9" />
                                                    <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                                                </svg>
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.pages.destroy', $page) }}"
                                                  id="delete-page-form-{{ $page->id }}" class="inline-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                        data-page-delete-trigger
                                                        data-page-id="{{ $page->id }}"
                                                        data-page-name="{{ $page->title }}"
                                                        data-page-active="{{ $page->is_active ? '1' : '0' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none"
                                                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                                        <path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                                                    </svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="overview-empty">
                    <p>No Pages yet. Add one to get started.</p>
                </div>
            @endif
        </div>
    </div>

    @if ($pages->total() > 5)
        <div class="admin-pagination">
            @php
                $perPage = (int) request('per_page', $pages->perPage());
                $pageWindow = 1;
                $startPage = max(1, $pages->currentPage() - $pageWindow);
                $endPage = min($pages->lastPage(), $pages->currentPage() + $pageWindow);
            @endphp

            <div class="admin-pagination-bar">
                <form method="GET" class="admin-per-page-form">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <label class="pagination-select-label" for="pages-per-page">
                        <span>Show</span>
                        <select id="pages-per-page" name="per_page" class="pagination-select" onchange="this.form.submit()">
                            @foreach ([5, 10, 25, 50, 100] as $option)
                                <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                            @endforeach
                        </select>
                        <span>of {{ $pages->total() }}</span>
                    </label>
                </form>

                <div class="pagination-list">
                    @if ($pages->onFirstPage())
                        <span class="pagination-link" aria-disabled="true">« Previous</span>
                    @else
                        <a href="{{ $pages->previousPageUrl() }}" class="pagination-link">« Previous</a>
                    @endif

                    @if ($startPage > 1)
                        <a href="{{ $pages->url(1) }}" class="pagination-link">1</a>
                        @if ($startPage > 2)
                            <span class="pagination-ellipsis">…</span>
                        @endif
                    @endif

                    @for ($page = $startPage; $page <= $endPage; $page++)
                        @if ($page === $pages->currentPage())
                            <span class="pagination-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $pages->url($page) }}" class="pagination-link">{{ $page }}</a>
                        @endif
                    @endfor

                    @if ($endPage < $pages->lastPage())
                        @if ($endPage < $pages->lastPage() - 1)
                            <span class="pagination-ellipsis">…</span>
                        @endif
                        <a href="{{ $pages->url($pages->lastPage()) }}" class="pagination-link">{{ $pages->lastPage() }}</a>
                    @endif

                    @if ($pages->hasMorePages())
                        <a href="{{ $pages->nextPageUrl() }}" class="pagination-link">Next »</a>
                    @else
                        <span class="pagination-link" aria-disabled="true">Next »</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Delete confirmation modal --}}
    <div class="confirm-modal" id="page-delete-modal" style="display:none">
        <div class="confirm-modal-backdrop" id="page-delete-backdrop"></div>
        <div class="confirm-modal-box">
            <h3 class="confirm-modal-title">Delete Page?</h3>
            <p class="confirm-modal-body" id="page-delete-text">
                This action cannot be undone.
            </p>
            <div class="confirm-modal-actions">
                <button type="button" class="btn btn-ghost" id="page-delete-cancel">Cancel</button>
                <button type="button" class="btn btn-danger-solid" id="page-delete-confirm">Delete</button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const _pageDeleteModal = document.getElementById('page-delete-modal');
    const _pageDeleteBackdrop = document.getElementById('page-delete-backdrop');
    const _pageDeleteCancel = document.getElementById('page-delete-cancel');
    const _pageDeleteConfirm = document.getElementById('page-delete-confirm');
    const _pageDeleteText = document.getElementById('page-delete-text');
    let _activePageDeleteForm = null;

    const _openPageDeleteModal = () => {
        if (_pageDeleteModal) _pageDeleteModal.style.display = '';
    };
    const _closePageDeleteModal = () => {
        if (_pageDeleteModal) _pageDeleteModal.style.display = 'none';
        _activePageDeleteForm = null;
    };

    document.querySelectorAll('[data-page-delete-trigger]').forEach((button) => {
        button.addEventListener('click', () => {
            _activePageDeleteForm = document.getElementById(`delete-page-form-${button.dataset.pageId}`);
            const isActive = button.dataset.pageActive === '1';
            const extra = isActive ? ' This is your currently ACTIVE page .' : '';
            _pageDeleteText.textContent = `Delete "${button.dataset.pageName}"? This cannot be undone.${extra}`;
            _openPageDeleteModal();
        });
    });

    _pageDeleteCancel?.addEventListener('click', _closePageDeleteModal);
    _pageDeleteBackdrop?.addEventListener('click', _closePageDeleteModal);
    _pageDeleteConfirm?.addEventListener('click', () => _activePageDeleteForm?.submit());
</script>
@endpush
