<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Pet;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public Pet $pet;
    public $prontuarios = [];
    public $vacinas = [];
    public $pesos = [];
    public $ultimoPeso = '--';
    public $abaAtiva = 'timeline';

    public function getTimeline()
    {
        $timeline = collect();

        foreach($this->pesos as $peso) {
            $timeline->push(['type' => 'peso', 'date' => $peso->recorded_at, 'data' => $peso]);
        }
        foreach($this->vacinas as $vacina) {
            $timeline->push(['type' => 'vacina', 'date' => $vacina->date_given, 'data' => $vacina]);
        }
        foreach($this->exames as $exame) {
            $timeline->push(['type' => 'exame', 'date' => $exame->date_performed, 'data' => $exame]);
        }
        foreach($this->diarios as $diario) {
            if ($diario->date_observed) {
                $timeline->push(['type' => 'diario', 'date' => $diario->date_observed->format('Y-m-d'), 'data' => $diario]);
            }
        }

        if ($this->pet->birth_date) {
            $timeline->push(['type' => 'nascimento', 'date' => \Carbon\Carbon::parse($this->pet->birth_date)->format('Y-m-d'), 'data' => $this->pet]);
        }

        return $timeline->sortByDesc('date')->values();
    }
    
    public $showModalPeso = false;
    public $novoPeso = '';
    public $dataPesagem = '';

    public $showModalVacina = false;
    public $novaVacinaNome = '';
    public $novaVacinaData = '';
    public $novaVacinaProxima = '';
    public $novaVacinaLote = '';

    public $exames = [];
    public $showModalExame = false;
    public $novoExameNome = '';
    public $novoExameData = '';
    public $novoExameNotas = '';
    public $novoExameArquivo;

    public $diarios = [];
    public $showModalDiario = false;
    public $novoDiarioTitulo = '';
    public $novoDiarioDescricao = '';
    public $novoDiarioData = '';



    public $showModalEditar = false;
    public $editName = '';
    public $editSpecies = '';
    public $editBreed = '';
    public $editGender = '';
    public $editBirthDate = '';
    public $editCoatColor = '';
    public $editEmergencyContact = '';
    public $editMedicalConditions = '';
    public $editPhoto;

    public function mount($pet_id)
    {
        $user = Auth::user();
        
        // Garante que o pet pertence a este tutor
        $this->pet = Pet::where('id', $pet_id)->where('user_id', $user->id)->firstOrFail();
        
        $this->prontuarios = []; // Módulo de consultas ainda não estruturado no DB
        
        $this->vacinas = $this->pet->vaccines()->orderByRaw('next_due_date IS NULL, next_due_date ASC')->get();
            
        $this->pesos = $this->pet->weights()->get();

        $this->exames = $this->pet->exams()->latest('date_performed')->get();
        $this->diarios = $this->pet->observations()->latest('date_observed')->get();

        $this->editName = $this->pet->name;
        $this->editSpecies = $this->pet->species;
        $this->editBreed = $this->pet->breed;
        $this->editGender = $this->pet->gender;
        $this->editBirthDate = $this->pet->birth_date ? \Carbon\Carbon::parse($this->pet->birth_date)->format('Y-m-d') : '';
        $this->editCoatColor = $this->pet->coat_color;
        $this->editEmergencyContact = $this->pet->emergency_contact;
        $this->editMedicalConditions = $this->pet->medical_conditions;
        
        $ultimoWeight = $this->pet->weights()->first();
        
        if ($ultimoWeight) {
            $this->ultimoPeso = number_format($ultimoWeight->weight, 1, ',', '.') . ' kg';
        } else {
            $this->ultimoPeso = '--';
        }
        
        $this->dataPesagem = date('Y-m-d');
    }

    public function salvarPeso()
    {
        $this->validate([
            'novoPeso' => 'required|numeric|min:0.1',
            'dataPesagem' => 'required|date',
        ]);

        $this->pet->weights()->create([
            'weight' => str_replace(',', '.', $this->novoPeso),
            'recorded_at' => $this->dataPesagem,
        ]);

        $this->pesos = $this->pet->weights()->get();
        $this->ultimoPeso = str_replace('.', ',', $this->novoPeso) . ' kg';
        
        $this->showModalPeso = false;
        $this->novoPeso = '';
    }

    public function salvarVacina()
    {
        $this->validate([
            'novaVacinaNome' => 'required|string|max:255',
            'novaVacinaData' => 'required|date',
            'novaVacinaProxima' => 'nullable|date|after_or_equal:novaVacinaData',
            'novaVacinaLote' => 'nullable|string|max:255',
        ]);

        $this->pet->vaccines()->create([
            'name' => $this->novaVacinaNome,
            'date_given' => $this->novaVacinaData,
            'next_due_date' => $this->novaVacinaProxima ?: null,
            'batch_number' => $this->novaVacinaLote,
        ]);

        $this->vacinas = $this->pet->vaccines()->orderByRaw('next_due_date IS NULL, next_due_date ASC')->get();
        
        $this->showModalVacina = false;
        $this->novaVacinaNome = '';
        $this->novaVacinaData = '';
        $this->novaVacinaProxima = '';
        $this->novaVacinaLote = '';
    }

    public function salvarExame()
    {
        $this->validate([
            'novoExameNome' => 'required|string|max:255',
            'novoExameData' => 'required|date',
            'novoExameNotas' => 'nullable|string|max:500',
            'novoExameArquivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        $filePath = null;
        if ($this->novoExameArquivo) {
            $filePath = $this->novoExameArquivo->store('exames', 'public');
        }

        $this->pet->exams()->create([
            'name' => $this->novoExameNome,
            'date_performed' => $this->novoExameData,
            'notes' => $this->novoExameNotas,
            'file_path' => $filePath,
        ]);

        $this->exames = $this->pet->exams()->latest('date_performed')->get();
        
        $this->showModalExame = false;
        $this->novoExameNome = '';
        $this->novoExameData = '';
        $this->novoExameNotas = '';
        $this->novoExameArquivo = null;
    }

    public function salvarDiario()
    {
        $this->validate([
            'novoDiarioTitulo' => 'nullable|string|max:255',
            'novoDiarioDescricao' => 'required|string',
            'novoDiarioData' => 'nullable|date',
        ]);

        $this->pet->observations()->create([
            'title' => $this->novoDiarioTitulo,
            'description' => $this->novoDiarioDescricao,
            'date_observed' => $this->novoDiarioData ?: now()->format('Y-m-d'),
        ]);

        $this->diarios = $this->pet->observations()->latest('date_observed')->get();
        
        $this->showModalDiario = false;
        $this->novoDiarioTitulo = '';
        $this->novoDiarioDescricao = '';
        $this->novoDiarioData = '';
    }

    public function salvarEdicao()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editSpecies' => 'nullable|string|max:255',
            'editBreed' => 'nullable|string|max:255',
            'editGender' => 'nullable|string|in:Macho,Fêmea',
            'editBirthDate' => 'nullable|date',
            'editCoatColor' => 'nullable|string|max:255',
            'editEmergencyContact' => 'nullable|string|max:20',
            'editMedicalConditions' => 'nullable|string|max:1000',
            'editPhoto' => 'nullable|image|max:2048',
        ]);

        if ($this->editPhoto) {
            $this->pet->photo_path = $this->editPhoto->store('pets', 'public');
        }

        $this->pet->update([
            'name' => $this->editName,
            'species' => $this->editSpecies,
            'breed' => $this->editBreed,
            'gender' => $this->editGender,
            'birth_date' => $this->editBirthDate,
            'coat_color' => $this->editCoatColor,
            'emergency_contact' => $this->editEmergencyContact,
            'medical_conditions' => $this->editMedicalConditions,
        ]);

        $this->showModalEditar = false;
        $this->editPhoto = null;
        $this->pet->refresh();
    }

    public function exportarProntuario()
    {
        $timeline = $this->getTimeline();
        $pet = $this->pet;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.prontuario', [
            'pet' => $pet,
            'timeline' => $timeline,
            'pesos' => $this->pesos,
            'vacinas' => $this->vacinas,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'prontuario_' . \Illuminate\Support\Str::slug($pet->name) . '.pdf');
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
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.2); border: 3px solid #fff; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; color: #fff; text-transform: uppercase; overflow: hidden;">
                        @if($pet->photo_path)
                            <img src="{{ Storage::url($pet->photo_path) }}" alt="{{ $pet->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            {{ substr($pet->name, 0, 1) }}
                        @endif
                    </div>
                    
                    <!-- Info -->
                    <div>
                        <h2 style="margin: 0 0 4px 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">{{ $pet->name }}</h2>
                        <p style="margin: 0; font-size: 14px; opacity: 0.9;">{{ ucfirst($pet->species ?? 'Espécie N/A') }} • {{ $pet->breed ?: 'Sem raça' }}</p>
                        <p style="margin: 4px 0 0 0; font-size: 13px; font-weight: 600; background: rgba(255,255,255,0.2); display: inline-block; padding: 2px 8px; border-radius: 10px;">{{ ucfirst($pet->gender ?? 'N/A') }}</p>
                    </div>
                </div>

                <!-- Botão de Editar -->
                <button wire:click="$set('showModalEditar', true)" style="position: absolute; top: 24px; right: 24px; z-index: 10; background: rgba(255,255,255,0.2); border: none; color: #fff; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'" title="Editar Pet">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </button>

                <!-- Dados vitais na carteirinha -->
                <div style="display: flex; justify-content: space-between; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.2); position: relative; z-index: 1;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; opacity: 0.8; font-weight: 600; margin-bottom: 4px;">Nascimento</div>
                        <div style="font-size: 15px; font-weight: 700;">{{ $pet->birth_date ? \Carbon\Carbon::parse($pet->birth_date)->format('d/m/Y') : '--' }}</div>
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

            <!-- Botão Exportar PDF -->
            <button wire:click="exportarProntuario" style="width: 100%; margin-top: 16px; background: #fff; border: 1px solid #E2E8F0; color: #0F766E; font-weight: 700; font-size: 15px; padding: 16px; border-radius: 16px; display: flex; justify-content: center; align-items: center; gap: 8px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);" onmouseover="this.style.borderColor='#0F766E'; this.style.boxShadow='0 10px 15px -3px rgba(15, 118, 110, 0.1)';" onmouseout="this.style.borderColor='#E2E8F0'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05)';">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Exportar Prontuário Médico
            </button>

            <!-- Botão Identidade Digital / QR Code -->
            <button onclick="document.getElementById('modal-qrcode').style.display='flex'" style="width: 100%; margin-top: 12px; background: #fff; border: 1px solid #E2E8F0; color: #DB2777; font-weight: 700; font-size: 15px; padding: 16px; border-radius: 16px; display: flex; justify-content: center; align-items: center; gap: 8px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);" onmouseover="this.style.borderColor='#DB2777'; this.style.boxShadow='0 10px 15px -3px rgba(219, 39, 119, 0.1)';" onmouseout="this.style.borderColor='#E2E8F0'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05)';">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                Identidade Digital (QR Code)
            </button>
        </div>

        <!-- Coluna Direita: Conteúdos (Tabs + Timeline) -->
        <div class="pet-right-col" style="padding-top: 20px;">
            <!-- Tabs (Segmented Control) -->
            <div style="padding: 0 20px; margin-bottom: 24px;">
                <div style="background: #EFEFEF; border-radius: 12px; padding: 4px; display: flex; gap: 4px; overflow-x: auto;">
                    <button wire:click="$set('abaAtiva', 'timeline')" style="flex: 1; min-width: 80px; border: none; background: {{ $abaAtiva === 'timeline' ? '#fff' : 'transparent' }}; color: {{ $abaAtiva === 'timeline' ? '#191919' : '#878787' }}; border-radius: 8px; padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: {{ $abaAtiva === 'timeline' ? '0 2px 4px rgba(0,0,0,0.05)' : 'none' }}; transition: 0.2s;">
                        Timeline
                    </button>
                    <button wire:click="$set('abaAtiva', 'pesos')" style="flex: 1; min-width: 70px; border: none; background: {{ $abaAtiva === 'pesos' ? '#fff' : 'transparent' }}; color: {{ $abaAtiva === 'pesos' ? '#191919' : '#878787' }}; border-radius: 8px; padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: {{ $abaAtiva === 'pesos' ? '0 2px 4px rgba(0,0,0,0.05)' : 'none' }}; transition: 0.2s;">
                        Peso
                    </button>
                    <button wire:click="$set('abaAtiva', 'diario')" style="flex: 1; min-width: 80px; border: none; background: {{ $abaAtiva === 'diario' ? '#fff' : 'transparent' }}; color: {{ $abaAtiva === 'diario' ? '#191919' : '#878787' }}; border-radius: 8px; padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: {{ $abaAtiva === 'diario' ? '0 2px 4px rgba(0,0,0,0.05)' : 'none' }}; transition: 0.2s;">
                        Diário
                    </button>
                    <button wire:click="$set('abaAtiva', 'vacinas')" style="flex: 1; min-width: 70px; border: none; background: {{ $abaAtiva === 'vacinas' ? '#fff' : 'transparent' }}; color: {{ $abaAtiva === 'vacinas' ? '#191919' : '#878787' }}; border-radius: 8px; padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: {{ $abaAtiva === 'vacinas' ? '0 2px 4px rgba(0,0,0,0.05)' : 'none' }}; transition: 0.2s;">
                        Vacinas
                    </button>
                    <button wire:click="$set('abaAtiva', 'exames')" style="flex: 1; min-width: 70px; border: none; background: {{ $abaAtiva === 'exames' ? '#fff' : 'transparent' }}; color: {{ $abaAtiva === 'exames' ? '#191919' : '#878787' }}; border-radius: 8px; padding: 10px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: {{ $abaAtiva === 'exames' ? '0 2px 4px rgba(0,0,0,0.05)' : 'none' }}; transition: 0.2s;">
                        Exames
                    </button>
                </div>
            </div>

            <!-- Conteúdo das Abas -->
            <div style="padding: 0 20px;">
                
                @if($abaAtiva === 'timeline')
                    <div wire:key="tab-timeline">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <h3 style="font-size: 18px; font-weight: 800; color: #191919; margin: 0;">Timeline Clínica</h3>
                        </div>

                    @php
                        $eventos = $this->getTimeline();
                    @endphp

                    @if($eventos->count() > 0)
                        <div style="position: relative; margin-left: 12px;">
                            <!-- Linha vertical da Timeline -->
                            <div style="position: absolute; left: 19px; top: 10px; bottom: 0; width: 2px; background: #EFEFEF; z-index: 0;"></div>

                            @foreach($eventos as $evento)
                                <div style="position: relative; z-index: 1; display: flex; gap: 16px; margin-bottom: 24px;">
                                    
                                    @if($evento['type'] === 'peso')
                                        <!-- Peso -->
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 2px solid #299D91; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; box-shadow: 0 2px 8px rgba(41, 157, 145, 0.15);">
                                            <svg width="20" height="20" fill="none" stroke="#299D91" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                        </div>
                                        <div style="background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); border: 1px solid #EFEFEF; flex: 1;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                <div style="font-weight: 700; color: #191919; font-size: 15px;">Registro de Peso</div>
                                                <div style="color: #878787; font-size: 12px; font-weight: 600;">{{ \Carbon\Carbon::parse($evento['date'])->format('d/m/Y') }}</div>
                                            </div>
                                            <p style="margin: 0; font-size: 14px; color: #525256;">O pet pesou <strong>{{ number_format($evento['data']->weight, 1, ',', '.') }} kg</strong>.</p>
                                        </div>

                                    @elseif($evento['type'] === 'vacina')
                                        <!-- Vacina -->
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 2px solid #3B82F6; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);">
                                            <svg width="20" height="20" fill="none" stroke="#3B82F6" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                        </div>
                                        <div style="background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); border: 1px solid #EFEFEF; flex: 1;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                <div style="font-weight: 700; color: #191919; font-size: 15px;">Vacina Aplicada</div>
                                                <div style="color: #878787; font-size: 12px; font-weight: 600;">{{ \Carbon\Carbon::parse($evento['date'])->format('d/m/Y') }}</div>
                                            </div>
                                            <p style="margin: 0; font-size: 14px; color: #525256;">Imunização: <strong>{{ $evento['data']->name }}</strong>.</p>
                                        </div>

                                    @elseif($evento['type'] === 'exame')
                                        <!-- Exame -->
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 2px solid #8B5CF6; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; box-shadow: 0 2px 8px rgba(139, 92, 246, 0.15);">
                                            <svg width="20" height="20" fill="none" stroke="#8B5CF6" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div style="background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); border: 1px solid #EFEFEF; flex: 1;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                <div style="font-weight: 700; color: #191919; font-size: 15px;">Exame Realizado</div>
                                                <div style="color: #878787; font-size: 12px; font-weight: 600;">{{ \Carbon\Carbon::parse($evento['date'])->format('d/m/Y') }}</div>
                                            </div>
                                            <p style="margin: 0; font-size: 14px; color: #525256;">Procedimento: <strong>{{ $evento['data']->name }}</strong>.</p>
                                        </div>

                                    @elseif($evento['type'] === 'diario')
                                        <!-- Diário -->
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 2px solid #F59E0B; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15);">
                                            <svg width="20" height="20" fill="none" stroke="#F59E0B" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </div>
                                        <div style="background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); border: 1px solid #EFEFEF; flex: 1;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                <div style="font-weight: 700; color: #191919; font-size: 15px;">Anotação: {{ $evento['data']->title ?: 'Geral' }}</div>
                                                <div style="color: #878787; font-size: 12px; font-weight: 600;">{{ \Carbon\Carbon::parse($evento['date'])->format('d/m/Y') }}</div>
                                            </div>
                                            <p style="margin: 0; font-size: 14px; color: #525256;">{{ \Illuminate\Support\Str::limit($evento['data']->description, 100) }}</p>
                                        </div>
                                    @elseif($evento['type'] === 'nascimento')
                                        <!-- Nascimento -->
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #fff; border: 2px solid #EC4899; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; box-shadow: 0 2px 8px rgba(236, 72, 153, 0.15);">
                                            <svg width="20" height="20" fill="none" stroke="#EC4899" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"/></svg>
                                        </div>
                                        <div style="background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.02); border: 1px solid #EFEFEF; flex: 1;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                <div style="font-weight: 700; color: #191919; font-size: 15px;">Nascimento de {{ $evento['data']->name }}</div>
                                                <div style="color: #878787; font-size: 12px; font-weight: 600;">{{ \Carbon\Carbon::parse($evento['date'])->format('d/m/Y') }}</div>
                                            </div>
                                            <p style="margin: 0; font-size: 14px; color: #525256;">O início de uma bela história juntos!</p>
                                        </div>
                                    @endif
                                    
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 20px; background: #fff; border-radius: 16px; border: 1px dashed #EFEFEF;">
                            <svg width="40" height="40" fill="none" stroke="#CBD5E1" viewBox="0 0 24 24" stroke-width="1.5" style="margin: 0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h4 style="font-size: 15px; font-weight: 700; color: #191919; margin: 0 0 4px 0;">Timeline vazia</h4>
                            <p style="font-size: 13px; color: #878787; margin: 0;">Conforme você registra eventos, eles aparecerão aqui ordenados.</p>
                        </div>
                    @endif
                    </div> <!-- end tab-timeline -->
                @elseif($abaAtiva === 'pesos')
                    <div wire:key="tab-pesos">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3 style="font-size: 18px; font-weight: 800; color: #191919; margin: 0;">Controle de Peso</h3>
                        <button wire:click="$set('showModalPeso', true)" style="background: #299D91; color: #fff; border: none; border-radius: 8px; padding: 8px 12px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(41, 157, 145, 0.3);">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Novo Peso
                        </button>
                    </div>

                    @if(count($pesos) > 1)
                        <div wire:ignore x-data="{
                                initChart() {
                                    if (typeof Chart === 'undefined') {
                                        let script = document.createElement('script');
                                        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                                        script.onload = () => this.drawChart();
                                        document.head.appendChild(script);
                                    } else {
                                        this.drawChart();
                                    }
                                },
                                drawChart() {
                                    const ctx = this.$refs.canvas.getContext('2d');
                                    const pesosData = @js($pesos->sortBy('recorded_at')->values());
                                    const labels = pesosData.map(w => {
                                        const datePart = w.recorded_at.split('T')[0];
                                        const [y, m, d] = datePart.split('-');
                                        return `${d}/${m}`;
                                    });
                                    const data = pesosData.map(w => w.weight);

                                    if(window.myWeightChart) { window.myWeightChart.destroy(); }

                                    window.myWeightChart = new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: labels,
                                            datasets: [{
                                                label: 'Peso (kg)',
                                                data: data,
                                                borderColor: '#299D91',
                                                backgroundColor: 'rgba(41, 157, 145, 0.1)',
                                                borderWidth: 3,
                                                pointBackgroundColor: '#fff',
                                                pointBorderColor: '#299D91',
                                                pointBorderWidth: 2,
                                                pointRadius: 4,
                                                fill: true,
                                                tension: 0.4
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { display: false } },
                                            scales: {
                                                y: { border: {display: false}, grid: { color: '#EFEFEF' } },
                                                x: { border: {display: false}, grid: { display: false } }
                                            }
                                        }
                                    });
                                }
                            }" x-init="initChart()" style="background: #fff; border-radius: 16px; padding: 16px; border: 1px solid #EFEFEF; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                            <div style="font-size: 14px; font-weight: 700; color: #191919; margin-bottom: 16px;">Evolução do Peso</div>
                            <div style="position: relative; height: 250px; width: 100%;">
                                <canvas x-ref="canvas"></canvas>
                            </div>
                        </div>
                    @endif

                    @if(count($pesos) > 0)
                        <div style="background: #fff; border-radius: 16px; border: 1px solid #EFEFEF; overflow: hidden; margin-bottom: 24px;">
                            @foreach($pesos as $index => $peso)
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; {{ !$loop->last ? 'border-bottom: 1px solid #EFEFEF;' : '' }}">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #F8F9FA; display: flex; align-items: center; justify-content: center; color: #191919;">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                        </div>
                                        <div>
                                            <div style="font-size: 15px; font-weight: 700; color: #191919;">{{ number_format($peso->weight, 1, ',', '.') }} kg</div>
                                            <div style="font-size: 13px; color: #878787;">Pesagem em {{ \Carbon\Carbon::parse($peso->recorded_at)->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                    @if($index < count($pesos) - 1)
                                        @php
                                            // A lista está ordenada descendentemente (mais recente primeiro)
                                            // Então o peso anterior no tempo é $pesos[$index + 1]
                                            $diff = $peso->weight - $pesos[$index + 1]->weight;
                                        @endphp
                                        @if($diff > 0)
                                            <div style="font-size: 13px; font-weight: 700; color: #DC2626; display: flex; align-items: center; gap: 4px;">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                                {{ number_format(abs($diff), 1, ',', '.') }} kg
                                            </div>
                                        @elseif($diff < 0)
                                            <div style="font-size: 13px; font-weight: 700; color: #16A34A; display: flex; align-items: center; gap: 4px;">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                                {{ number_format(abs($diff), 1, ',', '.') }} kg
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 20px; background: #fff; border-radius: 16px; border: 1px dashed #EFEFEF;">
                            <svg width="40" height="40" fill="none" stroke="#CBD5E1" viewBox="0 0 24 24" stroke-width="1.5" style="margin: 0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            <h4 style="font-size: 15px; font-weight: 700; color: #191919; margin: 0 0 4px 0;">Nenhum peso registrado</h4>
                            <p style="font-size: 13px; color: #878787; margin: 0;">Acompanhe o desenvolvimento do seu pet adicionando pesagens.</p>
                        </div>
                    @endif
                    </div> <!-- end tab-pesos -->
                @elseif($abaAtiva === 'diario')
                    <div wire:key="tab-diario">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3 style="font-size: 18px; font-weight: 800; color: #191919; margin: 0;">Diário de Observações</h3>
                        <button wire:click="$set('showModalDiario', true)" style="background: #299D91; color: #fff; border: none; border-radius: 8px; padding: 8px 12px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(41, 157, 145, 0.3);">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Nova Anotação
                        </button>
                    </div>

                    @if(count($diarios) > 0)
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            @foreach($diarios as $diario)
                                <div style="background: #fff; border-radius: 16px; padding: 16px; border: 1px solid #EFEFEF; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; align-items: flex-start; gap: 16px;">
                                    <div style="width: 48px; height: 48px; border-radius: 12px; background: #FFFBEB; color: #F59E0B; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #FEF3C7;">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                            <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #191919;">{{ $diario->title ?: 'Anotação Geral' }}</h4>
                                            <span style="font-size: 12px; font-weight: 600; color: #878787;">{{ $diario->date_observed ? $diario->date_observed->format('d/m/Y') : 'Data n/a' }}</span>
                                        </div>
                                        <p style="margin: 0; font-size: 14px; color: #525256; line-height: 1.5; white-space: pre-wrap;">{{ $diario->description }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 20px; background: #fff; border-radius: 16px; border: 1px dashed #EFEFEF;">
                            <svg width="40" height="40" fill="none" stroke="#CBD5E1" viewBox="0 0 24 24" stroke-width="1.5" style="margin: 0 auto 12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <h4 style="font-size: 15px; font-weight: 700; color: #191919; margin: 0 0 4px 0;">Nenhuma anotação</h4>
                            <p style="font-size: 13px; color: #878787; margin: 0;">Registre observações sobre comportamento, alergias ou trocas de ração do seu pet.</p>
                        </div>
                    @endif
                    </div> <!-- end tab-diario -->
                @elseif($abaAtiva === 'vacinas')
                    <div wire:key="tab-vacinas">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3 style="font-size: 18px; font-weight: 800; color: #191919; margin: 0;">Calendário de Vacinas</h3>
                        <button wire:click="$set('showModalVacina', true)" style="background: #299D91; color: #fff; border: none; border-radius: 8px; padding: 8px 12px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(41, 157, 145, 0.3);">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Nova Vacina
                        </button>
                    </div>
                    
                    @if(count($vacinas) > 0)
                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            @foreach($vacinas as $vac)
                                @php
                                    $isAtrasada = $vac->next_due_date && \Carbon\Carbon::parse($vac->next_due_date) < now()->startOfDay();
                                    $isProxima = $vac->next_due_date && \Carbon\Carbon::parse($vac->next_due_date) >= now()->startOfDay();
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
                                                <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #191919;">{{ $vac->name }}</h4>
                                                <p style="margin: 2px 0 0 0; font-size: 13px; color: #878787;">Aplicada em {{ $vac->date_given->format('d/m/Y') }}</p>
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
                                                <div style="font-size: 13px; font-weight: 600; color: #525256;">{{ $vac->batch_number ?: 'Não informado' }}</div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-size: 11px; text-transform: uppercase; font-weight: 600; color: {{ $isAtrasada ? '#DC2626' : '#9F9F9F' }}; margin-bottom: 2px;">Próxima Dose</div>
                                                <div style="font-size: 13px; font-weight: 700; color: {{ $isAtrasada ? '#DC2626' : '#191919' }};">
                                                    {{ $vac->next_due_date ? $vac->next_due_date->format('d/m/Y') : '--' }}
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
                    </div> <!-- end tab-vacinas -->
                @elseif($abaAtiva === 'exames')
                    <div wire:key="tab-exames">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                        <h3 style="font-size: 18px; font-weight: 800; color: #191919; margin: 0;">Laudos e Exames</h3>
                        <button wire:click="$set('showModalExame', true)" style="background: #299D91; color: #fff; border: none; border-radius: 8px; padding: 8px 12px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(41, 157, 145, 0.3);">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Adicionar Exame
                        </button>
                    </div>

                    @if(count($exames) > 0)
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
                            @foreach($exames as $exame)
                                <div style="background: #fff; border-radius: 16px; padding: 16px; border: 1px solid #EFEFEF; box-shadow: 0 2px 8px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; gap: 16px;">
                                    
                                    <div style="display: flex; gap: 12px; align-items: flex-start;">
                                        <div style="width: 48px; height: 48px; border-radius: 12px; background: #F8F9FA; color: #64748B; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div>
                                            <h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #191919;">{{ $exame->name }}</h4>
                                            <p style="margin: 2px 0 0 0; font-size: 13px; color: #878787;">{{ $exame->date_performed->format('d/m/Y') }}</p>
                                        </div>
                                    </div>

                                    @if($exame->notes)
                                        <div style="font-size: 13px; color: #525256; background: #F8F9FA; padding: 10px; border-radius: 8px;">
                                            {{ $exame->notes }}
                                        </div>
                                    @endif

                                    <div style="border-top: 1px solid #EFEFEF; padding-top: 12px;">
                                        @if($exame->file_path)
                                            <a href="{{ Storage::url($exame->file_path) }}" target="_blank" style="display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; background: #E8F5F3; color: #299D91; text-decoration: none; padding: 8px 0; border-radius: 8px; font-size: 13px; font-weight: 700; transition: 0.2s;">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                Ver Laudo
                                            </a>
                                        @else
                                            <div style="display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; background: #F8F9FA; color: #9F9F9F; padding: 8px 0; border-radius: 8px; font-size: 13px; font-weight: 600;">
                                                Sem anexo
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 20px; background: #fff; border-radius: 16px; border: 1px solid #EFEFEF; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                            <div style="width: 64px; height: 64px; border-radius: 16px; background: #F8F9FA; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                <svg width="32" height="32" fill="none" stroke="#64748B" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <h4 style="font-size: 16px; font-weight: 800; color: #191919; margin: 0 0 8px 0;">Nenhum exame registrado</h4>
                            <p style="font-size: 13px; color: #878787; margin: 0; line-height: 1.5;">Aqui ficarão salvos os laudos de laboratório e exames de imagem do seu pet.</p>
                        </div>
                    @endif
                    </div> <!-- end tab-exames -->
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Novo Peso -->
    @if($showModalPeso)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div style="background: #fff; border-radius: 20px; width: 100%; max-width: 400px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                <div style="padding: 20px; border-bottom: 1px solid #EFEFEF; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #191919;">Registrar Peso</h3>
                    <button wire:click="$set('showModalPeso', false)" style="background: none; border: none; cursor: pointer; color: #878787;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div style="padding: 20px;">
                    <form wire:submit.prevent="salvarPeso">
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Peso (kg)</label>
                            <input type="number" step="0.01" wire:model="novoPeso" placeholder="Ex: 4.5" required style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none;">
                            @error('novoPeso') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Data da Pesagem</label>
                            <input type="date" wire:model="dataPesagem" required style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                            @error('dataPesagem') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" style="width: 100%; background: #299D91; color: #fff; border: none; border-radius: 10px; padding: 14px; font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 10px rgba(41, 157, 145, 0.3);">
                            Salvar Registro
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Nova Vacina -->
    @if($showModalVacina)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div style="background: #fff; border-radius: 20px; width: 100%; max-width: 400px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                <div style="padding: 20px; border-bottom: 1px solid #EFEFEF; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #191919;">Registrar Vacina</h3>
                    <button wire:click="$set('showModalVacina', false)" style="background: none; border: none; cursor: pointer; color: #878787;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div style="padding: 20px; max-height: 70vh; overflow-y: auto;">
                    <form wire:submit.prevent="salvarVacina">
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Nome da Vacina</label>
                            <input type="text" wire:model="novaVacinaNome" placeholder="Ex: V10, Antirrábica" required style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                            @error('novaVacinaNome') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Data da Aplicação</label>
                            <input type="date" wire:model="novaVacinaData" required style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                            @error('novaVacinaData') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Próxima Dose (Opcional)</label>
                            <input type="date" wire:model="novaVacinaProxima" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                            @error('novaVacinaProxima') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Lote (Opcional)</label>
                            <input type="text" wire:model="novaVacinaLote" placeholder="Número do Lote" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                            @error('novaVacinaLote') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" style="width: 100%; background: #299D91; color: #fff; border: none; border-radius: 10px; padding: 14px; font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 10px rgba(41, 157, 145, 0.3);">
                            Salvar Vacina
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Novo Exame -->
    @if($showModalExame)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div style="background: #fff; border-radius: 20px; width: 100%; max-width: 400px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                <div style="padding: 20px; border-bottom: 1px solid #EFEFEF; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #191919;">Registrar Exame</h3>
                    <button wire:click="$set('showModalExame', false)" style="background: none; border: none; cursor: pointer; color: #878787;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div style="padding: 20px; max-height: 70vh; overflow-y: auto;">
                    <form wire:submit.prevent="salvarExame">
                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Nome do Exame</label>
                            <input type="text" wire:model="novoExameNome" placeholder="Ex: Hemograma Completo" required style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                            @error('novoExameNome') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Data do Exame</label>
                            <input type="date" wire:model="novoExameData" required style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                            @error('novoExameData') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Anexar Laudo (PDF ou Imagem)</label>
                            <input type="file" wire:model="novoExameArquivo" accept=".pdf,image/*" style="width: 100%; box-sizing: border-box; padding: 10px; border: 1px dashed #CBD5E1; border-radius: 10px; font-size: 13px; color: #525256;">
                            <div wire:loading wire:target="novoExameArquivo" style="color: #299D91; font-size: 12px; margin-top: 4px;">Carregando arquivo...</div>
                            @error('novoExameArquivo') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Observações</label>
                            <textarea wire:model="novoExameNotas" rows="2" placeholder="Opcional..." style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919; resize: none;"></textarea>
                            @error('novoExameNotas') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" style="width: 100%; background: #299D91; color: #fff; border: none; border-radius: 10px; padding: 14px; font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 10px rgba(41, 157, 145, 0.3);">
                            Salvar Exame
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Editar Pet -->
    @if($showModalEditar)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div style="background: #fff; border-radius: 20px; width: 100%; max-width: 500px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                <div style="padding: 20px; border-bottom: 1px solid #EFEFEF; display: flex; justify-content: space-between; align-items: center; background: #F8F9FA;">
                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #191919;">Editar Perfil do Pet</h3>
                    <button wire:click="$set('showModalEditar', false)" style="background: none; border: none; cursor: pointer; color: #878787;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div style="padding: 20px; max-height: 70vh; overflow-y: auto;">
                    <form wire:submit.prevent="salvarEdicao">
                        
                        <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                            <div style="width: 80px; height: 80px; border-radius: 50%; background: #F8F9FA; border: 1px solid #EFEFEF; flex-shrink: 0; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                @if($editPhoto)
                                    <img src="{{ $editPhoto->temporaryUrl() }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @elseif($pet->photo_path)
                                    <img src="{{ Storage::url($pet->photo_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <svg width="32" height="32" fill="none" stroke="#CBD5E1" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                @endif
                            </div>
                            <div style="flex: 1;">
                                <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Atualizar Foto</label>
                                <input type="file" wire:model="editPhoto" accept="image/*" style="width: 100%; box-sizing: border-box; padding: 10px; border: 1px dashed #CBD5E1; border-radius: 10px; font-size: 13px; color: #525256;">
                                <div wire:loading wire:target="editPhoto" style="color: #299D91; font-size: 12px; margin-top: 4px;">Carregando imagem...</div>
                                @error('editPhoto') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Nome</label>
                            <input type="text" wire:model="editName" required style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                            @error('editName') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Espécie</label>
                                <select wire:model="editSpecies" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919; background: #fff;">
                                    <option value="">Selecione</option>
                                    <option value="Cachorro">Cachorro</option>
                                    <option value="Gato">Gato</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Gênero</label>
                                <select wire:model="editGender" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919; background: #fff;">
                                    <option value="">Selecione</option>
                                    <option value="Macho">Macho</option>
                                    <option value="Fêmea">Fêmea</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Raça</label>
                                <input type="text" wire:model="editBreed" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Cor / Pelagem</label>
                                <input type="text" wire:model="editCoatColor" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                            </div>
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Data de Nascimento</label>
                            <input type="date" wire:model="editBirthDate" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                        </div>

                        <div style="margin-bottom: 16px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">WhatsApp / Contato de Emergência (com DDD)</label>
                            <input type="text" wire:model="editEmergencyContact" placeholder="Ex: 11999999999" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                        </div>

                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Condições Médicas / Remédios / Cuidados</label>
                            <textarea wire:model="editMedicalConditions" placeholder="Ex: Alérgico a dipirona, toma fenobarbital 2x ao dia..." rows="3" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919; resize: vertical;"></textarea>
                        </div>

                        <button type="submit" style="width: 100%; background: #299D91; color: #fff; border: none; border-radius: 10px; padding: 14px; font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 10px rgba(41, 157, 145, 0.3);">
                            <span wire:loading.remove wire:target="salvarEdicao">Salvar Alterações</span>
                            <span wire:loading wire:target="salvarEdicao">Salvando...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Novo Diário -->
    @if($showModalDiario)
        <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; display: flex; align-items: center; justify-content: center; padding: 20px;">
            <div style="background: #fff; border-radius: 20px; width: 100%; max-width: 500px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                <div style="padding: 20px; border-bottom: 1px solid #EFEFEF; display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #191919;">Nova Anotação no Diário</h3>
                    <button wire:click="$set('showModalDiario', false)" style="background: none; border: none; cursor: pointer; color: #878787;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div style="padding: 20px; max-height: 70vh; overflow-y: auto;">
                    <form wire:submit.prevent="salvarDiario">
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Título (Opcional)</label>
                                <input type="text" wire:model="novoDiarioTitulo" placeholder="Ex: Troca de Ração" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Data</label>
                                <input type="date" wire:model="novoDiarioData" style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919;">
                            </div>
                        </div>

                        <div style="margin-bottom: 24px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">O que você observou?</label>
                            <textarea wire:model="novoDiarioDescricao" required rows="4" placeholder="Descreva os sintomas, comportamentos diferentes, etc..." style="width: 100%; box-sizing: border-box; padding: 12px; border: 1px solid #EFEFEF; border-radius: 10px; font-size: 15px; outline: none; color: #191919; resize: vertical;"></textarea>
                            @error('novoDiarioDescricao') <span style="color: #DC2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" style="width: 100%; background: #299D91; color: #fff; border: none; border-radius: 10px; padding: 14px; font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 10px rgba(41, 157, 145, 0.3);">
                            <span wire:loading.remove wire:target="salvarDiario">Salvar Anotação</span>
                            <span wire:loading wire:target="salvarDiario">Salvando...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal QR Code -->
    <div id="modal-qrcode" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: #fff; border-radius: 20px; width: 100%; max-width: 400px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2); text-align: center;">
            <div style="padding: 20px; border-bottom: 1px solid #EFEFEF; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 800; color: #191919;">Identidade Digital</h3>
                <button onclick="document.getElementById('modal-qrcode').style.display='none'" style="background: none; border: none; cursor: pointer; color: #878787;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div style="padding: 30px;">
                <p style="color: #525256; font-size: 14px; margin-bottom: 20px;">Imprima este QR Code e coloque na coleira do seu pet. Se ele se perder, qualquer pessoa poderá escanear e ver a página pública para entrar em contato com você.</p>
                
                <div style="background: #F8F9FA; padding: 20px; border-radius: 16px; display: inline-block; margin-bottom: 20px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('pet.public', $pet->uuid ?? '123')) }}" alt="QR Code" style="width: 200px; height: 200px; border-radius: 8px;">
                </div>
                
                <a href="{{ route('pet.public', $pet->uuid ?? '123') }}" target="_blank" style="display: block; width: 100%; background: #DB2777; color: #fff; text-decoration: none; border-radius: 10px; padding: 14px; font-size: 15px; font-weight: 700; transition: 0.2s;">
                    Testar Página Pública
                </a>
            </div>
        </div>
    </div>
</div>
