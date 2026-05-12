<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') | {{ config('app.name', 'My AI App') }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { 
                background: #f1f5f9; 
                font-family: 'Figtree', sans-serif; 
                color: #0f172a; 
                background-image: 
                    radial-gradient(circle at 0% 0%, rgba(55, 114, 255, 0.05) 0%, transparent 50%),
                    radial-gradient(circle at 100% 100%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
                min-height: 100vh;
            }
            .public-note-container { max-width: 900px; margin: 80px auto; padding: 0 24px; }
            .public-note-card { 
                background: rgba(255, 255, 255, 0.95); 
                border-radius: 24px; 
                border: 1px solid rgba(255, 255, 255, 0.3); 
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); 
                padding: 60px; 
                backdrop-filter: blur(8px);
            }
            .public-note-header { margin-bottom: 40px; border-bottom: 1px solid #f1f5f9; padding-bottom: 30px; }
            .public-note-title { font-family: 'Space Grotesk', sans-serif; font-size: 2.5rem; font-weight: 700; color: #0f172a; margin: 0 0 12px 0; letter-spacing: -0.04em; line-height: 1.1; }
            .public-note-meta { color: #64748b; font-size: 0.95rem; font-weight: 500; }
            .public-note-content { font-size: 1.25rem; line-height: 1.8; color: #334155; white-space: pre-wrap; }
            .public-note-footer { margin-top: 60px; text-align: center; color: #94a3b8; font-size: 0.9rem; }
            .password-form { max-width: 400px; margin: 0 auto; }
            .btn-primary { background: linear-gradient(135deg, #1d4ed8, #0f766e); color: white; padding: 12px 24px; border-radius: 12px; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s; }
            .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(29, 78, 216, 0.2); }
            .form-control { width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e2e8f0; outline: none; transition: all 0.2s; }
            .form-control:focus { border-color: #1d4ed8; box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.1); }
        </style>
    </head>
    <body>
        <div class="public-note-container">
            @yield('content')
            <div class="public-note-footer">
                Shared via <a href="/" class="text-blue-600 font-semibold">{{ config('app.name') }}</a>
            </div>
        </div>
    </body>
</html>
