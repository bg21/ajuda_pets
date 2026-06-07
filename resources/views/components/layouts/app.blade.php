<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AjudaPet - Portal do Tutor' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-background text-slate-800 font-sans antialiased flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-primary-dark text-white flex flex-col z-20 shadow-xl">
        <!-- Logo -->
        <div class="h-20 flex items-center px-6 border-b border-teal-800/50">
            <div class="w-8 h-8 bg-accent rounded-lg flex items-center justify-center mr-3 shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path></svg>
            </div>
            <span class="font-bold text-xl tracking-wide">AjudaPet</span>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-4 py-6 space-y-1.5">
            <a href="{{ route('portal.dashboard') }}" class="flex items-center px-4 py-3 bg-teal-800/60 rounded-xl text-teal-50 font-medium transition shadow-sm border border-teal-700/50">
                <svg class="w-5 h-5 mr-3 text-accent" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                Dashboard
            </a>
            <a href="#" class="flex items-center px-4 py-3 rounded-xl text-teal-200 hover:bg-teal-800/40 hover:text-white transition font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                Meus Pets
            </a>
            <a href="#" class="flex items-center justify-between px-4 py-3 rounded-xl text-teal-200 hover:bg-teal-800/40 hover:text-white transition font-medium">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    Lembretes
                </div>
                <span class="bg-accent text-white text-xs font-bold px-2 py-0.5 rounded-full">3</span>
            </a>
        </nav>

        <!-- User -->
        <div class="p-4 border-t border-teal-800/50">
            <div class="flex items-center px-2 mb-3">
                <div class="w-9 h-9 rounded-full bg-accent flex items-center justify-center font-bold text-white shadow-sm text-sm mr-3">
                    {{ substr(Auth::user()->name ?? 'T', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ explode(' ', Auth::user()->name ?? 'Tutor')[0] }}</p>
                    <p class="text-xs text-teal-300 truncate">Minha Conta</p>
                </div>
            </div>
            <form method="POST" action="{{ route('portal.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-teal-800/40 hover:bg-red-500/80 rounded-lg text-teal-100 hover:text-white transition text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Sair
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden relative">
        <!-- Top Header -->
        <header class="h-16 bg-white/80 backdrop-blur-md shadow-sm border-b border-slate-100 flex items-center justify-between px-8 z-10 sticky top-0">
            <h1 class="text-xl font-bold text-slate-700">{{ $title ?? 'Dashboard' }}</h1>
            
            <div class="flex items-center space-x-4">
                <button class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-accent hover:bg-orange-50 transition border border-slate-100 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </div>
        </header>

        <!-- Dynamic Content (Slot) -->
        <div class="flex-1 overflow-y-auto p-8">
            {{ $slot }}
        </div>
    </main>

    @livewireScripts
</body>
</html>
