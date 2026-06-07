<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.tutor')] class extends Component {
    public $pets = [];
    public $tutorNome = '';

    public function mount()
    {
        $tutor = Auth::guard('tutor')->user();
        $this->tutorNome = explode(' ', trim($tutor->nome))[0];
        $this->pets = $tutor->pets()->get();
    }
}; ?>

<style>
    @media (min-width: 768px) {
        .mobile-header, .portal-bottom-nav { display: none !important; }
        .desktop-container { padding: 40px; max-width: 900px; margin: 0 auto; }
    }
    @media (max-width: 767px) {
        .desktop-pet-list { padding: 0 20px 10px 20px !important; }
    }
</style>

<div class="desktop-container" style="display: flex; flex-direction: column; background: #fff; min-height: 100%;">
    
    <!-- Top Header (Apenas Mobile) -->
    <div class="mobile-header" style="padding: 24px 20px 16px 20px; display: flex; justify-content: space-between; align-items: center; background: #fff;">
        <div style="font-size: 24px; font-weight: 800; color: #299D91; letter-spacing: -0.5px;">vet<span style="color:#191919">os</span><span style="color:#E53E3E">♥</span></div>
        <button style="background: none; border: none; padding: 0; cursor: pointer;">
            <svg width="24" height="24" fill="none" stroke="#191919" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    <!-- Título Saudação -->
    <div style="padding: 0 20px; margin-bottom: 24px;">
        <h1 style="font-size: 22px; font-weight: 700; color: #191919; margin: 0;">Olá, {{ $tutorNome }}</h1>
        <p style="font-size: 14px; color: #878787; margin: 4px 0 0 0;">Acompanhe a saúde da sua família</p>
    </div>

    <!-- Seção: Seus Pets (Carrossel Horizontal) -->
    <div style="padding: 0 0 24px 0;">
        <h2 style="font-size: 18px; font-weight: 700; color: #191919; margin: 0 0 16px 20px;">Seus pets</h2>
        
        <div class="desktop-pet-list" style="display: flex; gap: 16px; overflow-x: auto; padding-bottom: 8px; scrollbar-width: none; -ms-overflow-style: none;">
            <!-- Oculta scrollbar no Chrome/Safari -->
            <style>
                div::-webkit-scrollbar { display: none; }
            </style>

            @forelse($pets as $pet)
                <a href="{{ route('portal.pet', ['pet_id' => $pet->id]) }}" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 8px; flex-shrink: 0; cursor: pointer;">
                    <!-- Círculo do Pet (Avatar) -->
                    <div style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #E8F5F3 0%, #CCECE8 100%); border: 3px solid #299D91; display: flex; align-items: center; justify-content: center; color: #0F766E; font-size: 28px; font-weight: 800; text-transform: uppercase; box-shadow: 0 4px 10px rgba(41, 157, 145, 0.15);">
                        {{ substr($pet->nome, 0, 1) }}
                    </div>
                    <span style="font-size: 14px; font-weight: 600; color: #525256;">{{ explode(' ', trim($pet->nome))[0] }}</span>
                </a>
            @empty
                <div style="font-size: 13px; color: #9F9F9F; padding: 10px 0;">Você ainda não possui pets cadastrados.</div>
            @endforelse

            <!-- Botão Adicionar -->
            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; flex-shrink: 0; cursor: pointer;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: #F8F9FA; border: 2px dashed #CBD5E1; display: flex; align-items: center; justify-content: center; color: #64748B;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                </div>
                <span style="font-size: 14px; font-weight: 600; color: #64748B;">Adicionar</span>
            </div>
        </div>
    </div>

    <!-- Banner (Estilo Promotional) -->
    <div style="padding: 0 20px;">
        <div style="background: linear-gradient(135deg, #4338CA 0%, #312E81 100%); border-radius: 16px; padding: 24px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(67, 56, 202, 0.2);">
            <!-- Bolhas decorativas de fundo -->
            <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -30px; left: 40%; width: 80px; height: 80px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            
            <h3 style="font-size: 18px; font-weight: 800; margin: 0 0 8px 0; position: relative; z-index: 1;">Clube de Benefícios</h3>
            <p style="font-size: 13px; color: #E0E7FF; margin: 0 0 16px 0; line-height: 1.4; position: relative; z-index: 1; max-width: 80%;">
                Mantenha a saúde da sua família em dia e ganhe descontos nas próximas vacinas.
            </p>
            <button style="background: #fff; color: #4338CA; border: none; padding: 8px 16px; border-radius: 20px; font-size: 12px; font-weight: 700; cursor: pointer; position: relative; z-index: 1;">
                Saiba Mais
            </button>
        </div>
    </div>

    <!-- Bottom Navigation Bar -->
    <div class="portal-bottom-nav" style="position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #EFEFEF; display: flex; justify-content: space-around; padding: 12px 0; padding-bottom: calc(12px + env(safe-area-inset-bottom)); max-width: 480px; margin: 0 auto; z-index: 100;">
        
        <!-- Tab: Início (Ativo) -->
        <a href="{{ route('portal.dashboard') }}" style="display: flex; flex-direction: column; align-items: center; gap: 4px; text-decoration: none; color: #299D91;">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span style="font-size: 10px; font-weight: 700;">Início</span>
        </a>

        <!-- Tab: Financeiro -->
        <a href="{{ route('portal.financeiro') }}" style="display: flex; flex-direction: column; align-items: center; gap: 4px; text-decoration: none; color: #9F9F9F;">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            <span style="font-size: 10px; font-weight: 600;">Financeiro</span>
        </a>

        <!-- Tab: Agenda -->
        <a href="{{ route('portal.agendamentos') }}" style="display: flex; flex-direction: column; align-items: center; gap: 4px; text-decoration: none; color: #9F9F9F;">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span style="font-size: 10px; font-weight: 600;">Agenda</span>
        </a>

        <!-- Tab: Sair -->
        <a href="{{ route('portal.logout') }}" style="display: flex; flex-direction: column; align-items: center; gap: 4px; text-decoration: none; color: #9F9F9F;">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            <span style="font-size: 10px; font-weight: 600;">Sair</span>
        </a>

    </div>
</div>
