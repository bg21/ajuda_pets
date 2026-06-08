<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AjudaPet | Revolucione o cuidado com seu Pet</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .text-gradient {
            background: linear-gradient(135deg, #0f766e 0%, #059669 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .bg-grid-pattern {
            background-image: linear-gradient(to right, rgba(15, 118, 110, 0.05) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(15, 118, 110, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .blob-shape {
            border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
            animation: morph 8s ease-in-out infinite;
        }

        @keyframes morph {
            0%, 100% { border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%; }
            34% { border-radius: 70% 30% 50% 50% / 30% 30% 70% 70%; }
            67% { border-radius: 100% 60% 60% 100% / 100% 100% 60% 60%; }
        }

        .float-slow { animation: float 6s ease-in-out infinite; }
        .float-fast { animation: float 4s ease-in-out infinite; }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased selection:bg-teal-500 selection:text-white">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 glass-nav" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20 transition-all duration-300" :class="{ 'h-16': scrolled, 'h-20': !scrolled }">
                
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-3 cursor-pointer" onclick="window.scrollTo(0,0)">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-teal-500/30">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/></svg>
                    </div>
                    <span class="font-black text-2xl tracking-tight text-slate-900">Ajuda<span class="text-teal-600">Pet</span></span>
                </div>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#como-funciona" class="text-sm font-semibold text-slate-600 hover:text-teal-600 transition-colors">Como Funciona</a>
                    <a href="#funcionalidades" class="text-sm font-semibold text-slate-600 hover:text-teal-600 transition-colors">Recursos</a>
                    <a href="#depoimentos" class="text-sm font-semibold text-slate-600 hover:text-teal-600 transition-colors">Depoimentos</a>
                    
                    <div class="flex items-center space-x-4 pl-6 border-l border-slate-200">
                        @if (Route::has('portal.login'))
                            @auth
                                <a href="{{ route('portal.dashboard') }}" class="text-sm font-bold text-slate-900 hover:text-teal-600 transition-colors">Meu Portal</a>
                                <a href="{{ route('portal.dashboard') }}" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-full text-sm font-bold transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">Acessar</a>
                            @else
                                <a href="{{ route('portal.login') }}" class="text-sm font-bold text-slate-900 hover:text-teal-600 transition-colors">Entrar</a>
                                @if (Route::has('portal.login'))
                                    <a href="{{ route('portal.login') }}" class="bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white px-6 py-2.5 rounded-full text-sm font-bold transition-all shadow-lg shadow-teal-500/30 hover:shadow-teal-500/50 hover:-translate-y-0.5">Cadastrar Grátis</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden bg-white">
        <div class="absolute inset-0 bg-grid-pattern opacity-[0.4] z-0"></div>
        
        <!-- Abstract glowing blobs -->
        <div class="absolute top-0 right-0 -mr-40 -mt-40 w-96 h-96 rounded-full bg-teal-400/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-40 -mb-40 w-96 h-96 rounded-full bg-emerald-400/20 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-8">
                
                <!-- Hero Text -->
                <div class="w-full lg:w-1/2 text-center lg:text-left pt-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-teal-50 border border-teal-100 text-teal-700 font-semibold text-sm mb-6 shadow-sm">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                        </span>
                        A revolução na saúde veterinária chegou
                    </div>
                    
                    <h1 class="text-5xl lg:text-6xl xl:text-7xl font-black tracking-tight text-slate-900 mb-6 leading-[1.1]">
                        A vida do seu pet <br />
                        <span class="text-gradient">na palma da mão.</span>
                    </h1>
                    
                    <p class="text-lg lg:text-xl text-slate-600 mb-10 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        O primeiro prontuário digital inteligente para tutores. Controle vacinas, exames, peso e crie um diário de saúde incrível para o seu melhor amigo.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="{{ route('portal.login') }}" class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white px-8 py-4 rounded-full font-bold text-lg transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 flex items-center justify-center gap-2">
                            Comece Gratuitamente
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <div class="flex -space-x-4 justify-center">
                            <img class="w-12 h-12 rounded-full border-4 border-white object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" alt="Usuário">
                            <img class="w-12 h-12 rounded-full border-4 border-white object-cover" src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=100&q=80" alt="Usuário">
                            <img class="w-12 h-12 rounded-full border-4 border-white object-cover" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80" alt="Usuário">
                            <div class="w-12 h-12 rounded-full border-4 border-white bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">+10k</div>
                        </div>
                    </div>
                </div>
                
                <!-- Hero Image Container -->
                <div class="w-full lg:w-1/2 relative">
                    <!-- Decorative background blob -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-teal-200 to-emerald-100 blob-shape transform scale-105 opacity-50"></div>
                    
                    <div class="relative rounded-[2rem] p-3 bg-white/40 backdrop-blur-sm border border-white/60 shadow-2xl z-10">
                        <img src="{{ asset('images/hero_pets.png') }}" alt="Cachorro e Gato no AjudaPet" class="w-full h-auto rounded-[1.5rem] object-cover shadow-inner max-h-[550px] object-center">
                        
                        <!-- Floating Badge 1 (Top Left) -->
                        <div class="absolute -top-6 -left-6 md:top-10 md:-left-12 bg-white/90 backdrop-blur p-4 rounded-2xl shadow-xl border border-white flex items-center gap-3 float-slow z-20">
                            <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-900 uppercase tracking-wider">Saúde</p>
                                <p class="text-sm text-slate-600 font-medium">100% em dia</p>
                            </div>
                        </div>

                        <!-- Floating Badge 2 (Bottom Right) -->
                        <div class="absolute -bottom-8 -right-4 md:bottom-12 md:-right-10 bg-white/90 backdrop-blur p-4 rounded-2xl shadow-xl border border-white flex items-center gap-4 float-fast z-20">
                            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">Vacina V10</p>
                                <p class="text-xs text-emerald-600 font-bold">Aplicada hoje</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="border-y border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-slate-100">
                <div>
                    <p class="text-3xl md:text-4xl font-black text-slate-900 mb-1">15k+</p>
                    <p class="text-sm text-slate-500 font-medium uppercase tracking-wide">Pets Felizes</p>
                </div>
                <div>
                    <p class="text-3xl md:text-4xl font-black text-slate-900 mb-1">50k+</p>
                    <p class="text-sm text-slate-500 font-medium uppercase tracking-wide">Vacinas Registradas</p>
                </div>
                <div>
                    <p class="text-3xl md:text-4xl font-black text-slate-900 mb-1">99%</p>
                    <p class="text-sm text-slate-500 font-medium uppercase tracking-wide">Tutores Satisfeitos</p>
                </div>
                <div>
                    <p class="text-3xl md:text-4xl font-black text-slate-900 mb-1">24/7</p>
                    <p class="text-sm text-slate-500 font-medium uppercase tracking-wide">Acesso aos Dados</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Split Section: The Problem / Solution -->
    <div id="como-funciona" class="py-24 bg-slate-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <!-- Image Side -->
                <div class="w-full lg:w-1/2 order-2 lg:order-1">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-teal-200 rounded-full mix-blend-multiply filter blur-3xl opacity-40"></div>
                        <img src="{{ asset('images/features_dog.png') }}" alt="Cachorro feliz correndo no parque" class="relative rounded-[2rem] shadow-2xl ring-1 ring-slate-900/5 object-cover w-full h-[500px]">
                        
                        <!-- Floating Element -->
                        <div class="absolute -left-4 top-1/4 md:-left-8 md:top-1/4 bg-white p-4 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-3 float-slow">
                            <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center font-bold">
                                +1kg
                            </div>
                            <div class="pr-2">
                                <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Peso Ideal</p>
                                <p class="font-bold text-slate-900">Atingido! 🎉</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Text Side -->
                <div class="w-full lg:w-1/2 order-1 lg:order-2">
                    <h2 class="text-sm font-bold text-teal-600 uppercase tracking-widest mb-3">O Fim da Caderneta de Papel</h2>
                    <h3 class="text-4xl md:text-5xl font-black text-slate-900 mb-6 leading-[1.15]">Mais tempo de qualidade,<br><span class="text-teal-600">menos preocupação.</span></h3>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        Nós sabemos que a vida é corrida. O AjudaPet foi criado para que você não precise confiar na sua memória para saber quando é a próxima vacina ou onde guardou o último exame de sangue do seu pet. 
                    </p>
                    
                    <ul class="space-y-6">
                        <li class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900">Histórico Centralizado</h4>
                                <p class="text-slate-600 mt-1">Acesse todos os dados médicos do seu pet em segundos, de qualquer lugar.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900">Consultas mais precisas</h4>
                                <p class="text-slate-600 mt-1">Compartilhe informações detalhadas e gráficos de peso com seu veterinário de confiança.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="funcionalidades" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-sm font-bold text-teal-600 uppercase tracking-widest mb-3">Recursos Exclusivos</h2>
                <h3 class="text-4xl md:text-5xl font-black text-slate-900 mb-6">Tudo o que você precisa em um app</h3>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">Organize a vida do seu pet com ferramentas projetadas com amor e muita tecnologia.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-[2rem] p-8 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100 group">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-400 to-teal-600 rounded-2xl flex items-center justify-center text-white mb-8 group-hover:scale-110 transition-transform shadow-md shadow-teal-500/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Carteira de Vacinas</h3>
                    <p class="text-slate-600 leading-relaxed">Diga adeus à caderneta de papel. Tenha o registro completo e seguro de todas as imunizações na palma da mão.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-[2rem] p-8 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100 group">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-2xl flex items-center justify-center text-white mb-8 group-hover:scale-110 transition-transform shadow-md shadow-emerald-500/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Evolução de Peso</h3>
                    <p class="text-slate-600 leading-relaxed">Acompanhe o desenvolvimento com gráficos interativos fáceis de ler para garantir que seu pet está saudável.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-[2rem] p-8 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100 group">
                    <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center text-white mb-8 group-hover:scale-110 transition-transform shadow-md shadow-amber-500/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Diário de Observações</h3>
                    <p class="text-slate-600 leading-relaxed">Anote mudanças de ração, sintomas ou comportamentos diferentes para ter relatórios fiéis pro seu veterinário.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-white rounded-[2rem] p-8 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100 group">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center text-white mb-8 group-hover:scale-110 transition-transform shadow-md shadow-purple-500/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Histórico de Exames</h3>
                    <p class="text-slate-600 leading-relaxed">Faça o upload de laudos em PDF e guarde todos os exames de sangue ou imagem de forma organizada na nuvem.</p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white rounded-[2rem] p-8 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100 group">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center text-white mb-8 group-hover:scale-110 transition-transform shadow-md shadow-blue-500/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Timeline Clínica</h3>
                    <p class="text-slate-600 leading-relaxed">Uma linha do tempo inteligente com absolutamente todos os eventos e registros ordenados cronologicamente.</p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white rounded-[2rem] p-8 shadow-lg shadow-slate-200/50 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-slate-100 group">
                    <div class="w-16 h-16 bg-gradient-to-br from-rose-400 to-rose-600 rounded-2xl flex items-center justify-center text-white mb-8 group-hover:scale-110 transition-transform shadow-md shadow-rose-500/20">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Múltiplos Pets</h3>
                    <p class="text-slate-600 leading-relaxed">Cadastre todos os animais da casa. Separe perfeitamente os perfis com fotos, raça, pelagem e dados individuais.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonial Section -->
    <div id="depoimentos" class="py-24 bg-slate-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
        <div class="absolute top-0 right-0 -mr-40 -mt-40 w-96 h-96 rounded-full bg-teal-500/20 blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-sm font-bold text-teal-400 uppercase tracking-widest mb-3">Depoimentos</h2>
                <h3 class="text-4xl md:text-5xl font-black text-white mb-6">Quem usa, ama.</h3>
            </div>

            <div x-data="{ activeSlide: 0, total: 2 }" class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Slides -->
                <div class="relative overflow-hidden py-4">
                    <div class="flex transition-transform duration-500 ease-in-out" :style="'transform: translateX(-' + (activeSlide * 100) + '%)'">
                        
                        <!-- Slide 1 -->
                        <div class="w-full flex-shrink-0 px-2">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Testimonial 1 -->
                                <div class="bg-slate-800/50 backdrop-blur border border-slate-700 rounded-[2rem] p-8 relative h-full flex flex-col justify-between">
                                    <div>
                                        <div class="absolute -top-6 right-8 text-6xl text-teal-500 opacity-50 font-serif">"</div>
                                        <div class="flex items-center gap-4 mb-6">
                                            <img src="{{ asset('images/testimonial_tutor.png') }}" alt="Mariana" class="w-16 h-16 rounded-full object-cover border-2 border-teal-500">
                                            <div>
                                                <h4 class="text-xl font-bold text-white">Mariana & Thor</h4>
                                                <div class="flex text-amber-400 text-sm">★★★★★</div>
                                            </div>
                                        </div>
                                        <p class="text-slate-300 italic leading-relaxed">
                                            "Antes do AjudaPet, eu vivia perdendo a caderneta de vacinas do Thor. Agora, eu chego no veterinário, abro o celular e mostro a Timeline completa dele. O médico achou o máximo!"
                                        </p>
                                    </div>
                                </div>

                                <!-- Testimonial 2 -->
                                <div class="bg-slate-800/50 backdrop-blur border border-slate-700 rounded-[2rem] p-8 relative h-full flex flex-col justify-between">
                                    <div>
                                        <div class="absolute -top-6 right-8 text-6xl text-teal-500 opacity-50 font-serif">"</div>
                                        <div class="flex items-center gap-4 mb-6">
                                            <img src="{{ asset('images/testimonial_cat.png') }}" alt="Lucas" class="w-16 h-16 rounded-full object-cover border-2 border-teal-500">
                                            <div>
                                                <h4 class="text-xl font-bold text-white">Lucas & Luna</h4>
                                                <div class="flex text-amber-400 text-sm">★★★★★</div>
                                            </div>
                                        </div>
                                        <p class="text-slate-300 italic leading-relaxed">
                                            "O módulo de Diário de Observações me salvou. A Luna estava trocando de ração e eu anotei o comportamento dela. Isso ajudou o veterinário a fazer o diagnóstico perfeito."
                                        </p>
                                    </div>
                                </div>

                                <!-- Testimonial 3 -->
                                <div class="bg-slate-800/50 backdrop-blur border border-slate-700 rounded-[2rem] p-8 relative h-full flex flex-col justify-between">
                                    <div>
                                        <div class="absolute -top-6 right-8 text-6xl text-teal-500 opacity-50 font-serif">"</div>
                                        <div class="flex items-center gap-4 mb-6">
                                            <div class="w-16 h-16 rounded-full flex items-center justify-center bg-teal-600 text-white text-2xl font-bold border-2 border-teal-400">R</div>
                                            <div>
                                                <h4 class="text-xl font-bold text-white">Ricardo & Max</h4>
                                                <div class="flex text-amber-400 text-sm">★★★★★</div>
                                            </div>
                                        </div>
                                        <p class="text-slate-300 italic leading-relaxed">
                                            "Como resgatador de animais de rua, eu precisava de um sistema para organizar todos. O plano Max caiu como uma luva. Eu exporto os prontuários em PDF direto pro meu WhatsApp."
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div class="w-full flex-shrink-0 px-2">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Testimonial 4 -->
                                <div class="bg-slate-800/50 backdrop-blur border border-slate-700 rounded-[2rem] p-8 relative h-full flex flex-col justify-between">
                                    <div>
                                        <div class="absolute -top-6 right-8 text-6xl text-teal-500 opacity-50 font-serif">"</div>
                                        <div class="flex items-center gap-4 mb-6">
                                            <div class="w-16 h-16 rounded-full flex items-center justify-center bg-rose-500 text-white text-2xl font-bold border-2 border-rose-300">B</div>
                                            <div>
                                                <h4 class="text-xl font-bold text-white">Beatriz & Mel</h4>
                                                <div class="flex text-amber-400 text-sm">★★★★★</div>
                                            </div>
                                        </div>
                                        <p class="text-slate-300 italic leading-relaxed">
                                            "A identidade digital foi o que mais me chamou a atenção. Imprimi o QR Code e coloquei na coleira da Mel. Me sinto muito mais segura ao sair para passear com ela no parque."
                                        </p>
                                    </div>
                                </div>

                                <!-- Testimonial 5 -->
                                <div class="bg-slate-800/50 backdrop-blur border border-slate-700 rounded-[2rem] p-8 relative h-full flex flex-col justify-between">
                                    <div>
                                        <div class="absolute -top-6 right-8 text-6xl text-teal-500 opacity-50 font-serif">"</div>
                                        <div class="flex items-center gap-4 mb-6">
                                            <div class="w-16 h-16 rounded-full flex items-center justify-center bg-indigo-500 text-white text-2xl font-bold border-2 border-indigo-300">T</div>
                                            <div>
                                                <h4 class="text-xl font-bold text-white">Thiago & Simba</h4>
                                                <div class="flex text-amber-400 text-sm">★★★★★</div>
                                            </div>
                                        </div>
                                        <p class="text-slate-300 italic leading-relaxed">
                                            "Uso a plataforma todos os dias. O gráfico de evolução de peso é incrível, pois o Simba estava um pouco acima do peso ideal e o veterinário pediu para eu acompanhar de perto."
                                        </p>
                                    </div>
                                </div>

                                <!-- Testimonial 6 -->
                                <div class="bg-slate-800/50 backdrop-blur border border-slate-700 rounded-[2rem] p-8 relative h-full flex flex-col justify-between">
                                    <div>
                                        <div class="absolute -top-6 right-8 text-6xl text-teal-500 opacity-50 font-serif">"</div>
                                        <div class="flex items-center gap-4 mb-6">
                                            <div class="w-16 h-16 rounded-full flex items-center justify-center bg-amber-500 text-white text-2xl font-bold border-2 border-amber-300">J</div>
                                            <div>
                                                <h4 class="text-xl font-bold text-white">Julia & Paçoca</h4>
                                                <div class="flex text-amber-400 text-sm">★★★★★</div>
                                            </div>
                                        </div>
                                        <p class="text-slate-300 italic leading-relaxed">
                                            "Melhor investimento que eu fiz. Antes era uma papelada sem fim, agora está tudo na nuvem. Até a recepcionista da clínica adorou quando eu mandei o PDF com os exames!"
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Controls -->
                <button @click="activeSlide = activeSlide === 0 ? total - 1 : activeSlide - 1" class="absolute left-0 top-1/2 -translate-y-1/2 -ml-2 md:-ml-12 w-10 h-10 md:w-12 md:h-12 bg-slate-800 border border-slate-700 rounded-full flex items-center justify-center text-teal-400 hover:bg-slate-700 transition-colors z-10 shadow-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button @click="activeSlide = activeSlide === total - 1 ? 0 : activeSlide + 1" class="absolute right-0 top-1/2 -translate-y-1/2 -mr-2 md:-mr-12 w-10 h-10 md:w-12 md:h-12 bg-slate-800 border border-slate-700 rounded-full flex items-center justify-center text-teal-400 hover:bg-slate-700 transition-colors z-10 shadow-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>

                <!-- Dots -->
                <div class="flex justify-center gap-2 mt-8">
                    <template x-for="i in total" :key="i">
                        <button @click="activeSlide = i - 1" :class="{'w-8 bg-teal-500': activeSlide === i - 1, 'w-3 bg-slate-600 hover:bg-slate-500': activeSlide !== i - 1}" class="h-3 rounded-full transition-all duration-300"></button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing -->
    <div id="planos" class="py-24 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-sm font-bold text-teal-600 uppercase tracking-widest mb-3">Planos e Preços</h2>
                <h3 class="text-4xl md:text-5xl font-black text-slate-900 mb-6">Planos que cabem no seu bolso</h3>
                <p class="text-xl text-slate-600 max-w-2xl mx-auto">Comece gratuitamente e expanda caso a sua família cresça. Simples assim.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch max-w-6xl mx-auto">
                
                <!-- Básico (Até 3 pets) -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-lg shadow-slate-200/50 border border-slate-100 flex flex-col hover:border-teal-300 transition-colors">
                    <h4 class="text-xl font-bold text-slate-900 mb-2">Iniciante</h4>
                    <p class="text-slate-500 mb-6 text-sm">Para tutores com até 3 animais.</p>
                    <div class="mb-8">
                        <span class="text-5xl font-black text-slate-900">Grátis</span>
                    </div>
                    <ul class="space-y-4 mb-8 flex-grow">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-teal-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg><span class="text-slate-700">Até 3 pets cadastrados</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-teal-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg><span class="text-slate-700">Timeline Médica</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-teal-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg><span class="text-slate-700">Armazenamento em Nuvem</span></li>
                    </ul>
                    <a href="{{ route('portal.login') }}" class="block w-full py-4 px-6 rounded-full text-center font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 transition-colors border border-teal-100">
                        Começar Agora
                    </a>
                </div>

                <!-- Pro (4 a 10 pets) -->
                <div class="relative transform md:-translate-y-4 flex flex-col">
                    <div class="absolute -top-4 inset-x-0 flex justify-center z-20">
                        <span class="bg-gradient-to-r from-teal-400 to-emerald-500 text-white text-xs font-bold uppercase tracking-widest py-1.5 px-4 rounded-full shadow-lg">Mais Popular</span>
                    </div>
                    <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-2xl shadow-teal-900/20 border border-slate-700 flex flex-col relative overflow-hidden flex-grow">
                        <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-teal-400 to-emerald-500"></div>
                        <h4 class="text-xl font-bold text-white mb-2 mt-2">Pro</h4>
                        <p class="text-slate-400 mb-6 text-sm">A família cresceu? De 4 até 10 pets.</p>
                    <div class="mb-8 flex items-baseline gap-1">
                        <span class="text-3xl font-bold text-slate-400">R$</span>
                        <span class="text-6xl font-black text-white">4<span class="text-4xl">,99</span></span>
                        <span class="text-slate-400 ml-1">/mês</span>
                    </div>
                    <ul class="space-y-4 mb-8 flex-grow">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-teal-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg><span class="text-slate-200">De 4 até 10 pets</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-teal-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg><span class="text-slate-200">Todos os recursos do Iniciante</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-teal-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg><span class="text-slate-200">Suporte Prioritário</span></li>
                    </ul>
                    <a href="{{ route('portal.register', ['plan' => 'pro']) }}" class="block w-full py-4 px-6 rounded-full text-center font-bold text-slate-900 bg-white hover:bg-slate-100 transition-colors shadow-lg">
                        Assinar Plano Pro
                    </a>
                </div>
                </div>

                <!-- Max (Acima de 10) -->
                <div class="bg-white rounded-[2.5rem] p-8 shadow-lg shadow-slate-200/50 border border-slate-100 flex flex-col hover:border-teal-300 transition-colors">
                    <h4 class="text-xl font-bold text-slate-900 mb-2">Max</h4>
                    <p class="text-slate-500 mb-6 text-sm">Resgatadores, abrigos ou muitos pets.</p>
                    <div class="mb-8 flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-slate-400">R$</span>
                        <span class="text-5xl font-black text-slate-900">9<span class="text-3xl">,99</span></span>
                        <span class="text-slate-500 ml-1 text-sm">/mês</span>
                    </div>
                    <ul class="space-y-4 mb-8 flex-grow">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-teal-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg><span class="text-slate-700 font-bold">Pets Ilimitados (10+)</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-teal-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg><span class="text-slate-700">Todos os recursos do Pro</span></li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-teal-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg><span class="text-slate-700">Acesso antecipado a novidades</span></li>
                    </ul>
                    <a href="{{ route('portal.register', ['plan' => 'max']) }}" class="block w-full py-4 px-6 rounded-full text-center font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 transition-colors border border-teal-100">
                        Assinar Plano Max
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Huge Final CTA Section -->
    <div class="bg-gradient-to-br from-teal-600 to-emerald-800 py-24 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
        <div class="max-w-4xl mx-auto px-4 relative z-10 text-center">
            <h2 class="text-4xl md:text-5xl font-black text-white mb-6">Mude a vida do seu pet hoje</h2>
            <p class="text-teal-100 text-xl mb-10 max-w-2xl mx-auto">Mais de 15.000 tutores já estão aproveitando o AjudaPet. Leva apenas 30 segundos para começar.</p>
            <a href="{{ route('portal.login') }}" class="inline-block bg-white text-teal-900 px-10 py-5 rounded-full font-bold text-xl transition-transform hover:scale-105 shadow-2xl">
                Acessar Portal do Tutor
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white pt-16 pb-8 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-xl flex items-center justify-center text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/></svg>
                        </div>
                        <span class="font-black text-2xl tracking-tight text-slate-900">Ajuda<span class="text-teal-600">Pet</span></span>
                    </div>
                    <p class="text-slate-500 max-w-sm mb-6">O primeiro portal inteligente dedicado inteiramente a facilitar o cuidado e a gestão da saúde de animais de estimação no Brasil.</p>
                    <div class="flex gap-4 text-slate-400">
                        <!-- Social Icons -->
                        <a href="#" class="hover:text-teal-600 transition-colors"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                        <a href="#" class="hover:text-teal-600 transition-colors"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-bold text-slate-900 mb-4 uppercase text-sm tracking-wider">Produto</h4>
                    <ul class="space-y-3">
                        <li><a href="#funcionalidades" class="text-slate-500 hover:text-teal-600 transition-colors">Funcionalidades</a></li>
                        <li><a href="#depoimentos" class="text-slate-500 hover:text-teal-600 transition-colors">Depoimentos</a></li>
                        <li><a href="{{ route('portal.login') }}" class="text-slate-500 hover:text-teal-600 transition-colors">Login Tutores</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold text-slate-900 mb-4 uppercase text-sm tracking-wider">AjudaPet</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-slate-500 hover:text-teal-600 transition-colors">Sobre Nós</a></li>
                        <li><a href="#" class="text-slate-500 hover:text-teal-600 transition-colors">Termos de Uso</a></li>
                        <li><a href="#" class="text-slate-500 hover:text-teal-600 transition-colors">Privacidade</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-200 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-slate-400 text-sm font-medium mb-4 md:mb-0">© {{ date('Y') }} AjudaPet. Feito com ❤️ para os animais.</p>
                <div class="flex gap-4">
                    <span class="px-3 py-1 bg-slate-100 rounded-full text-xs font-bold text-slate-500 uppercase tracking-wider">Brasil</span>
                </div>
            </div>
        </div>
    </footer>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
