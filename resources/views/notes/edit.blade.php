@extends('layouts.admin')

@section('title', 'Edit Note')

@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="breadcrumb-separator"><polyline points="9 18 15 12 9 6"/></svg>
    <a href="{{ route('notes.index') }}">Notes</a>
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="breadcrumb-separator"><polyline points="9 18 15 12 9 6"/></svg>
    <span class="breadcrumb-active">Edit</span>
@endsection

@section('page-title', 'Edit Note')
@section('page-subtitle', 'Refine your ideas and keep your knowledge up to date.')

@section('content')
    <div class="editor-container">

        <form action="{{ route('notes.update', $note) }}" method="POST" class="editor-form">
            @csrf
            @method('PUT')
            <div class="admin-card editor-card">
                <div class="editor-header">
                    <input type="text" name="title" id="title" class="editor-title-input @error('title') is-invalid @enderror" 
                           placeholder="Note Title" value="{{ old('title', $note->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback px-8">{{ $message }}</div>
                    @enderror
                </div>
                <div class="editor-body">
                    <textarea name="content" id="content" class="editor-textarea @error('content') is-invalid @enderror" 
                              placeholder="Start typing your thoughts..." rows="15"
                              maxlength="{{ \App\Models\Note::MAX_CONTENT_LENGTH }}">{{ old('content', $note->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback px-8">{{ $message }}</div>
                    @enderror
                </div>

                <div class="editor-footer">
                    <div class="char-count-wrapper">
                        <span id="char-count">{{ strlen($note->content) }}</span> / {{ \App\Models\Note::MAX_CONTENT_LENGTH }} characters
                        <p id="limit-warning" class="text-danger text-xs mt-1 {{ strlen($note->content) >= \App\Models\Note::MAX_CONTENT_LENGTH ? '' : 'hidden' }} font-bold">Character limit reached!</p>
                    </div>
                    <button type="button" class="btn btn-secondary flex items-center" id="fix-grammar-btn">
                        <span id="fix-grammar-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 14px; height: 14px; vertical-align: middle; margin-right: 4px; display: inline-block;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </span>
                        <span id="fix-grammar-loader" class="hidden">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        Fix Grammar
                    </button>
                    <div class="flex gap-3">
                        <a href="{{ route('notes.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-8">Update Note</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        .editor-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .content-heading {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
        }
        .editor-card {
            border-radius: 24px;
            overflow: hidden;
            border: none;
            box-shadow: 0 30px 60px -12px rgba(15, 23, 42, 0.12);
        }
        .editor-header {
            padding: 32px 32px 16px;
            border-bottom: 1px solid rgba(0,0,0,0.13);
        }
        .editor-title-input {
            width: 100%;
            border: none;
            background: transparent;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text);
            padding: 0;
            outline: none;
        }
        .editor-title-input::placeholder {
            color: var(--text-subtle);
            opacity: 0.5;
        }
        .editor-body {
            padding: 16px 32px 32px;
        }
        .editor-textarea {
            width: 100%;
            border: none;
            background: transparent;
            font-family: 'Figtree', sans-serif;
            font-size: 1.1rem;
            line-height: 1.7;
            color: var(--text);
            padding: 0;
            outline: none;
            resize: none;
            min-height: 400px;
        }
        .editor-textarea::placeholder {
            color: var(--text-subtle);
            opacity: 0.5;
        }
        .editor-footer {
            padding: 20px 32px;
            background: #f8fafc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(0,0,0,0.13);
        }
        .is-invalid {
            border-bottom: 1px solid var(--danger) !important;
        }
        .hidden { display: none; }
        .text-danger {
            color: var(--danger) !important;
        }
    </style>

    <script>
        const textarea = document.getElementById('content');
        const charCount = document.getElementById('char-count');
        const warning = document.getElementById('limit-warning');
        const maxLen = {{ \App\Models\Note::MAX_CONTENT_LENGTH }};
        
        textarea.addEventListener('input', () => {
            const currentLen = textarea.value.length;
            charCount.innerText = currentLen;
            
            if (currentLen >= maxLen) {
                charCount.classList.add('text-danger', 'font-bold');
                warning.classList.remove('hidden');
            } else {
                charCount.classList.remove('text-danger', 'font-bold');
                warning.classList.add('hidden');
            }
        });

        // AI Grammar Fix logic
        const fixBtn = document.getElementById('fix-grammar-btn');
        const fixIcon = document.getElementById('fix-grammar-icon');
        const fixLoader = document.getElementById('fix-grammar-loader');

        fixBtn.addEventListener('click', async () => {
            const content = textarea.value.trim();
            if (!content) {
                if (window.createToast) {
                    window.createToast({
                        type: 'warning',
                        title: 'Empty Content',
                        message: 'Please enter some text to fix.'
                    });
                }
                return;
            }

            if(content.length < 3){
                if (window.createToast) {
                    window.createToast({
                        type: 'warning',
                        title: 'Content Too Short',
                        message: 'Please enter at least 3 characters.'
                    });
                }
                return;
            }
            
            if(content.length > maxLen){
                if (window.createToast) {
                    window.createToast({
                        type: 'error',
                        title: 'Content Too Long',
                        message: 'Please reduce the content length.'
                    });
                }
                return;
            }


            // Show loader
            fixBtn.disabled = true;
            fixIcon.classList.add('hidden');
            fixLoader.classList.remove('hidden');

            try {
                const response = await fetch('{{ route('notes.fix-grammar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content: content })
                });

                if (!response.ok) throw new Error('Failed to fix grammar');

                const data = await response.json();
                textarea.value = data.content;
                
                // Trigger input event to update char count
                textarea.dispatchEvent(new Event('input'));

                if (window.createToast) {
                    window.createToast({
                        type: 'success',
                        title: 'Success',
                        message: 'Grammar fixed successfully!'
                    });
                }
            } catch (error) {
                console.error(error);
                if (window.createToast) {
                    window.createToast({
                        type: 'error',
                        title: 'Error',
                        message: 'Something went wrong while fixing grammar.'
                    });
                }
            } finally {
                // Hide loader
                fixBtn.disabled = false;
                fixIcon.classList.remove('hidden');
                fixLoader.classList.add('hidden');
            }
        });
    </script>
@endsection
