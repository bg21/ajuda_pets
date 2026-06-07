<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Pet;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.tutor')] class extends Component {
    public Pet $pet;
    public $prontuarios = [];
    public $vacinas = [];
    public $ultimoPeso = '--';
    public $abaAtiva = 'consultas';

    public function mount($pet_id)
    {
        $tutor = Auth::guard('tutor')->user();
        
        // Garante que o pet pertence a este tutor
        $this->pet = Pet::where('id', $pet_id)->where('tutor_id', $tutor->id)->firstOrFail();
        
        $this->prontuarios = $this->pet->prontuarios()->latest()->get();
        
        $this->vacinas = \App\Models\PetVacina::with(['vacina', 'veterinario'])
            ->where('pet_id', $this->pet->id)
            ->orderByRaw('data_proxima_dose IS NULL, data_proxima_dose ASC')
            ->get();
        
        $ultimo = $this->pet->prontuarios()->whereNotNull('peso')->latest()->first();
        if ($ultimo) {
            $this->ultimoPeso = $ultimo->peso . ' kg';
        }
    }
}; ?>

<div class="pet-desktop-container" style="display: flex; flex-direction: column; background: #F8F9FA; min-height: 100%;">
    
    <style>
        @media (min-width: 768px) {
            .mobile-header { display: none !important; }
            .pet-desktop-container { padding: 40px; max-width: 1000px; margin: 0 auto; background: transparent !important; }
            .pet-split-layout { display: flex; gap: 40px; align-items: flex-start; }
            .pet-left-col { width: 380px; flex-shrink: 0; }
            .pet-right-col { flex: 1; }
        }
        @media (max-width: 767px) {
            .pet-split-layout { display: flex; flex-direction: column; }
        }
    </style>

    <!-- Top Header c/ Botão Voltar (Apenas Mobile) -->
    <div class="mobile-header" style="padding: 24px 20px 16px 20px; display: flex; justify-content: space-between; align-items: center; background: #fff;">
        <a href="{{ route('portal.dashboard') }}" style="color: #191919; text-decoration: none; display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 15px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Voltar
        </a>
        <div style="font-size: 18px; font-weight: 800; color: #191919;">Carteirinha</div>
        <div style="width: 20px;"></div> <!-- Spacer -->
    </div>

    <div class="pet-split-layout">
        
        <!-- Coluna Esquerda: Carteirinha Digital -->
        <div class="pet-left-col" style="padding: 20px;">
            <div style="background: linear-gradient(135deg, #299D91 0%, #0F766E 100%); border-radius: 20px; padding: 24px; color: #fff; position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(41, 157, 145, 0.25);">
                
                <div style="display: flex; gap: 20px; align-items: center; position: relative; z-index: 1;">
                    <!-- Foto -->
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.2); border: 3px solid #fff; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; color: #fff; text-transform: uppercase;">
                        {{ substr($pet->nome, 0, 1) }}
                    </div>
                    
                    <!-- Info -->
                    <div>
                        <h2 style="margin: 0 0 4px 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">{{ $pet->nome }}</h2>
                        <p style="margin: 0; font-size: 14px; opacity: 0.9;">{{ ucfirst($pet->especie) }} • {{ $pet->raca ?: 'Sem raça' }}</p>
                        <p style="margin: 4px 0 0 0; font-size: 13px; font-weight: 600; background: rgba(255,255,255,0.2); display: inline-block; padding: 2px 8px; border-radius: 10px;">{{ ucfirst($pet->sexo) }}</p>
                    </div>
                </div>

                <!-- Dados vitais na carteirinha -->
                <div style="display: flex; justify-content: space-between; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.2); position: relative; z-index: 1;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; opacity: 0.8; font-weight: 600; margin-bottom: 4px;">Nascimento</div>
                        <div style="font-size: 15px; font-weight: 700;">{{ $pet->data_nascimento ? \Carbon\Carbon::parse($pet->data_nascimento)->format('d/m/Y') : '--' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; opacity: 0.8; font-weight: 600; margin-bottom: 4px;">Último Peso</div>
                        <div style="font-size: 15px; font-weight: 700;">{{ $ultimoPeso }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; opacity: 0.8; font-weight: 600; margin-bottom: 4px;">ID Clínico</div>
                        <div style="font-size: 15px; font-weight: 700;">#{{ str_pad($pet->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>

                <!-- Decorativo -->
                <svg style="position: absolute; right: -20px; bottom: -20px; opacity: 0.1; width: 150px; height: 150px;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
            </div>
        </div>

        <!-- Coluna Direita: Conteúdos (Tabs + Timeline) -->
        <div class="pet-right-col" style="padding-top: 20px;">
            <!-- Tabs (Segmented Control) -->
            <div style="padding: 0 20px; margin-bottom: 24px;">
                <div style="background: #EFEFEF; border-radius: 12px; padding: 4px; display: flex; gap: 4px;">
                    <button wire:click="$set('abaAtiva', 'consultas')" style="flex: 1; border: none; background: {{ $abaAtiva === 'consultas' ? '#fff' : 'transparent' }}; color: {{ $abaAtiva === 'consultas' ? '#191919' : '#878787' }}; border-radius: 8px; padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: {{ $abaAtiva === 'consultas' ? '0 2px 4px rgba(0,0,0,0.05)' : 'none' }}; transition: 0.2s;">
                        Consultas
                    </button>
                    <button wire:click="$set('abaAtiva', 'vacinas')" style="flex: 1; border: none; background: {{ $abaAtiva === 'vacinas' ? '#fff' : 'transparent' }}; color: {{ $abaAtiva === 'vacinas' ? '#191919' : '#878787' }}; border-radius: 8px; padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: {{ $abaAtiva === 'vacinas' ? '0 2px 4px rgba(0,0,0,0.05)' : 'none' }}; transition: 0.2s;">
                        Vacinas
                    </button>
                    <button wire:click="$set('abaAtiva', 'exames')" style="flex: 1; border: none; background: {{ $abaAtiva === 'exames' ? '#fff' : 'transparent' }}; color: {{ $abaAtiva === 'exames' ? '#191919' : '#878787' }}; border-radius: 8px; padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: {{ $abaAtiva === 'exames' ? '0 2px 4px rgba(0,0,0,0.05)' : 'none' }}; transition: 0.2s;">
                        Exames
                    </button>
                </div>
            </div>

            <!-- Conteúdo das Abas -->
            <div style="padding: 0 20px;">
                
                @if($abaAtiva === 'consultas')
                    <h3 style="font-size: 18px; font-weight: 800; color: #191919; margin: 0 0 16px 0;">Histórico Clínico</h3>

                    @if(count($prontuarios) > 0)
                        <div style="position: relative; margin-left: 12px;">
                            <!-- Linha vertical da Timeline -->
                            <div style="position: absolute; left: 19px; top: 10px; bottom: 0; width: 2px; background: #EFEFEF; z-index: 0;"></div>

                            @foreach($prontuarios as $prontuario)
                                <div style="position: relative; z-index: 1; display: flex; gap: 16px; margin-bottom: 24px;">
                                    <!-- Bolinha da Timeline -->
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 2px solid #299D91; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; box-shadow: 0 2px 8px rgba(41, 157, 145, 0.15);">
                                        <svg width="20" height="20" fill="none" stroke="#299D91" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    
                                    <!-- Conteúdo do Atendimento -->
                                    <div style="background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); border: 1px solid #EFEFEF; flex: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                            <div style="font-weight: 700; color: #191919; font-size: 15px;">Consulta Médica</div>
                                            <div style="color: #878787; font-size: 12px; font-weight: 600;">{{ $prontuario->created_at->format('d/m/Y') }}</div>
                                        </div>
                                        
                                        <div style="background: #F8F9FA; padding: 12px; border-radius: 8px;">
                                            <p style="margin: 0; font-size: 13px; color: #525256;"><strong>Motivo:</strong> {{ $prontuario->queixa_principal ?: 'Checkup de rotina' }}</p>
                                            @if($prontuario->diagnostico)
                                                <p style="margin: 8px 0 0 0; font-size: 13px; color: #525256;"><strong>Diagnóstico:</strong> {{ $prontuario->diagnostico }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 32px 0; background: #fff; border-radius: 16px; border: 1px dashed #EFEFEF;">
                            <svg width="40" height="40" fill="none" stroke="#CBD5E1" viewBox="0 0 24 24" stroke-width="1.5" style="margin: 0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <p style="font-size: 14px; color: #9F9F9F; margin: 0;">Nenhum atendimento registrado na clínica.</p>
                        </div>
                    @endif

                @elseif($abaAtiva === 'vacinas')
                    <h3 style="font-size: 18px; font-weight: 800; color: #191919; margin: 0 0 16px 0;">Calendário de Vacinas</h3>
                    
                    @if(count($vacinas) > 0)
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            @foreach($vacinas as $vac)
                                @php
                                    $isAtrasada = $vac->data_proxima_dose && $vac->data_proxima_dose < now()->startOfDay();
                                    $isProxima = $vac->data_proxima_dose && $vac->data_proxima_dose >= now()->startOfDay();
                                @endphp
                                <div style="background: #fff; border-radius: 16px; padding: 16px; border: 1px solid {{ $isAtrasada ? '#FEE2E2' : '#EFEFEF' }}; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; align-items: flex-start; gap: 16px; position: relative; overflow: hidden;">
                                    
                                    @if($isAtrasada)
                                        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: #DC2626;"></div>
                                    @elseif($isProxima)
                                        <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: #299D91;"></div>
                                    @endif

                                    <div style="width: 48px; height: 48px; border-radius: 12px; background: {{ $isAtrasada ? '#FEE2E2' : '#E8F5F3' }}; color: {{ $isAtrasada ? '#DC2626' : '#299D91' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    </div>

                                    <div style="flex: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                            <div>
                                                <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #191919;">{{ $vac->vacina->nome ?? 'Vacina Removida' }}</h4>
                                                <p style="margin: 2px 0 0 0; font-size: 13px; color: #878787;">Aplicada em {{ $vac->data_aplicacao->format('d/m/Y') }}</p>
                                            </div>
                                            @if($isAtrasada)
                                                <span style="background: #FEE2E2; color: #DC2626; font-size: 11px; font-weight: 800; padding: 4px 8px; border-radius: 6px;">ATRASADA</span>
                                            @elseif($isProxima)
                                                <span style="background: #E6F4F1; color: #299D91; font-size: 11px; font-weight: 800; padding: 4px 8px; border-radius: 6px;">PROTEGIDO</span>
                                            @else
                                                <span style="background: #F8F9FA; color: #64748B; font-size: 11px; font-weight: 800; padding: 4px 8px; border-radius: 6px;">HISTÓRICO</span>
                                            @endif
                                        </div>

                                        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #EFEFEF; display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-size: 11px; text-transform: uppercase; font-weight: 600; color: #9F9F9F; margin-bottom: 2px;">Lote</div>
                                                <div style="font-size: 13px; font-weight: 600; color: #525256;">{{ $vac->lote ?: 'Não informado' }}</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-size: 11px; text-transform: uppercase; font-weight: 600; color: {{ $isAtrasada ? '#DC2626' : '#9F9F9F' }}; margin-bottom: 2px;">Próxima Dose</div>
                                                <div style="font-size: 13px; font-weight: 700; color: {{ $isAtrasada ? '#DC2626' : '#191919' }};">
                                                    {{ $vac->data_proxima_dose ? $vac->data_proxima_dose->format('d/m/Y') : '--' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 20px; background: #fff; border-radius: 16px; border: 1px dashed #EFEFEF;">
                            <svg width="40" height="40" fill="none" stroke="#CBD5E1" viewBox="0 0 24 24" stroke-width="1.5" style="margin: 0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            <h4 style="font-size: 15px; font-weight: 700; color: #191919; margin: 0 0 4px 0;">Nenhuma vacina registrada</h4>
                            <p style="font-size: 13px; color: #878787; margin: 0;">O histórico de vacinação do seu pet aparecerá aqui.</p>
                        </div>
                    @endif

                @elseif($abaAtiva === 'exames')
                    <h3 style="font-size: 18px; font-weight: 800; color: #191919; margin: 0 0 16px 0;">Laudos e Exames</h3>
                    <div style="text-align: center; padding: 40px 20px; background: #fff; border-radius: 16px; border: 1px solid #EFEFEF; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                        <div style="width: 64px; height: 64px; border-radius: 16px; background: #F8F9FA; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                            <svg width="32" height="32" fill="none" stroke="#64748B" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h4 style="font-size: 16px; font-weight: 800; color: #191919; margin: 0 0 8px 0;">Visualização de Resultados</h4>
                        <p style="font-size: 13px; color: #878787; margin: 0; line-height: 1.5;">Em breve você poderá baixar os PDFs dos exames de laboratório e imagens do seu pet com um clique.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
