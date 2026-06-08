<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Pet;
use App\Models\Weight;
use Illuminate\Support\Facades\Auth;
use App\Services\AsaasService;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public bool $showAddPetModal = false;

    public $name = '';
    public $species = '';
    public $breed = '';
    public $gender = '';
    public $birth_date = '';
    public $coat_color = '';
    public $photo;

    // Weight properties
    public bool $showWeightModal = false;
    public $weight_pet_id = '';
    public $weight_value = '';
    public $weight_recorded_at = '';

    public function mount()
    {
        $this->weight_recorded_at = now()->format('Y-m-d');
    }

    #[\Livewire\Attributes\Computed]
    public function canAddPet()
    {
        $user = Auth::user();
        $petCount = $user->pets()->count();
        
        $plan = ($user->subscription_status === 'ACTIVE') ? strtolower($user->plan_type) : 'free';
        
        if ($plan === 'max') return true;
        if ($plan === 'pro' && $petCount < 10) return true;
        if ($plan === 'free' && $petCount < 3) return true;
        
        return false;
    }

    public function savePet()
    {
        if (!$this->canAddPet()) {
            return;
        }

        $this->validate([
            'name' => 'required|string|max:255',
            'species' => 'nullable|string|max:255',
            'breed' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:Macho,Fêmea',
            'birth_date' => 'nullable|date',
            'coat_color' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('pets', 'public');
        }

        Pet::create([
            'user_id' => Auth::id(),
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => $this->name,
            'species' => $this->species,
            'breed' => $this->breed,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'coat_color' => $this->coat_color,
            'photo_path' => $photoPath,
        ]);

        $this->reset(['name', 'species', 'breed', 'gender', 'birth_date', 'coat_color', 'photo']);
        $this->showAddPetModal = false;
    }

    public function saveWeight()
    {
        $this->validate([
            'weight_pet_id' => 'required|exists:pets,id',
            'weight_value' => 'required|numeric|min:0',
            'weight_recorded_at' => 'required|date',
        ]);

        $pet = Pet::where('id', $this->weight_pet_id)->where('user_id', Auth::id())->firstOrFail();

        Weight::create([
            'pet_id' => $pet->id,
            'weight' => $this->weight_value,
            'recorded_at' => $this->weight_recorded_at,
        ]);

        $this->reset(['weight_value']);
        $this->weight_recorded_at = now()->format('Y-m-d');
        $this->showWeightModal = false;
    }
    public function syncSubscription(AsaasService $asaas)
    {
        $user = Auth::user();
        if ($user->subscription_status === 'PENDING' && $user->asaas_subscription_id) {
            $result = $asaas->getSubscription($user->asaas_subscription_id);
            if ($result['success'] && isset($result['data']['status'])) {
                if ($result['data']['status'] === 'ACTIVE') {
                    $user->update(['subscription_status' => 'ACTIVE']);
                    session()->flash('message', 'Pagamento confirmado! Sua assinatura está ativa.');
                } else {
                    session()->flash('message', 'O pagamento ainda não foi processado pelo Asaas.');
                }
            }
        }
    }
}; ?>

<div class="space-y-6 pb-10">
    <x-slot name="title">Dashboard</x-slot>

    @if (session()->has('message'))
        <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl font-bold flex items-center justify-between mb-4 border border-emerald-100" x-data="{ show: true }" x-show="show">
            <span>{{ session('message') }}</span>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    @endif

    <!-- Cabeçalho Principal -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-slate-800">Olá, {{ Auth::user()->name ?? 'Tutor' }}! 👋</h2>
                
                @if(Auth::user()->plan_type !== 'free')
                    @if(Auth::user()->subscription_status === 'ACTIVE')
                        <span class="bg-teal-100 text-teal-800 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider border border-teal-200 shadow-sm flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Plano {{ ucfirst(Auth::user()->plan_type) }}
                        </span>
                    @elseif(Auth::user()->subscription_status === 'PENDING')
                        <div class="flex items-center gap-2">
                            <a href="{{ route('portal.checkout', ['plan' => Auth::user()->plan_type]) }}" class="bg-orange-100 text-orange-800 hover:bg-orange-200 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider border border-orange-200 shadow-sm transition-colors cursor-pointer" title="Finalizar Pagamento">
                                Pagamento Pendente
                            </a>
                            <button wire:click="syncSubscription" class="bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-full p-1 transition-colors" title="Verificar Pagamento">
                                <svg class="w-4 h-4" wire:loading.class="animate-spin" wire:target="syncSubscription" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                        </div>
                    @endif
                @else
                    <span class="bg-slate-100 text-slate-600 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider border border-slate-200 shadow-sm">
                        Plano Grátis
                    </span>
                @endif
            </div>
            <p class="text-slate-500 text-base mt-1">Acompanhe a saúde e o desenvolvimento dos seus pets.</p>
        </div>
        <button wire:click="$set('showAddPetModal', true)" class="bg-accent hover:bg-accent-dark text-white font-semibold px-6 py-2.5 rounded-xl shadow-md transition-all flex items-center space-x-2 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Adicionar Pet</span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- COLUNA PRINCIPAL -->
        <div class="lg:col-span-2 space-y-6">
            
            @forelse(Auth::user()->pets as $pet)
            <!-- Cartão do Perfil do Pet -->
            <a href="{{ route('portal.pet', ['pet_id' => $pet->id]) }}" class="block bg-white rounded-2xl p-6 shadow-sm border border-slate-200 flex flex-col md:flex-row items-center gap-6 relative overflow-hidden hover:shadow-md hover:border-teal-300 transition-all cursor-pointer group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-teal-50 rounded-full blur-3xl -mr-20 -mt-20 pointer-events-none"></div>
                
                <div class="relative shrink-0">
                    <div class="w-28 h-28 rounded-xl overflow-hidden shadow-sm border border-slate-100 bg-slate-50 flex items-center justify-center">
                        @if($pet->photo_path)
                            <img src="{{ Storage::url($pet->photo_path) }}" alt="{{ $pet->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl font-bold text-slate-300">{{ substr($pet->name, 0, 1) }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex-1 z-10 w-full text-center md:text-left">
                    <div class="flex flex-col md:flex-row md:items-center gap-3 mb-1 justify-center md:justify-start">
                        <h3 class="text-xl font-bold text-slate-800">{{ $pet->name }}</h3>
                        @if($pet->breed)
                        <span class="bg-teal-50 text-primary border border-teal-100 font-semibold px-2 py-0.5 rounded-lg text-xs">{{ $pet->breed }}</span>
                        @endif
                    </div>
                    <p class="text-slate-500 text-sm mb-4">
                        {{ $pet->gender ?? 'Gênero n/a' }} • 
                        {{ $pet->birth_date ? $pet->birth_date->age . ' anos' : 'Idade n/a' }} • 
                        {{ $pet->coat_color ?? 'Cor n/a' }}
                    </p>
                    
                    <div class="flex justify-center md:justify-start gap-6">
                        <div>
                            <p class="text-xs text-slate-400 font-semibold uppercase mb-1">Peso Atual</p>
                            <p class="text-lg font-bold text-slate-800">
                                @if($pet->weights->count() > 0)
                                    {{ $pet->weights->first()->weight }} <span class="text-xs font-normal text-slate-500">kg</span>
                                @else
                                    -- <span class="text-xs font-normal text-slate-500">kg</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-semibold uppercase mb-1">Próx. Vacina</p>
                            <p class="text-lg font-bold text-slate-800">
                                @if($pet->vaccines->whereNotNull('return_date')->count() > 0)
                                    {{ $pet->vaccines->whereNotNull('return_date')->sortBy('return_date')->first()->return_date->format('d M') }}
                                @else
                                    --
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="bg-white rounded-2xl p-10 shadow-sm border border-slate-200 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center text-primary mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Nenhum pet cadastrado</h3>
                <p class="text-slate-500 mb-6">Você ainda não adicionou nenhum pet para acompanhar. Clique no botão acima para começar!</p>
                <button wire:click="$set('showAddPetModal', true)" class="bg-primary hover:bg-teal-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-sm transition-all cursor-pointer">
                    Adicionar Meu Primeiro Pet
                </button>
            </div>
            @endforelse



        </div>

        <!-- COLUNA LATERAL (Lembretes) -->
        <div class="space-y-6">
            
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-800">Lembretes</h3>
                    <span class="bg-orange-50 text-accent font-semibold text-xs px-2 py-1 rounded-md border border-orange-100">2 Pendentes</span>
                </div>

                <div class="space-y-3">
                    <div class="p-3.5 rounded-xl bg-orange-50/50 border border-orange-100 flex items-center space-x-3 cursor-pointer hover:bg-orange-50 transition">
                        <div class="w-12 h-12 rounded-lg bg-white flex flex-col items-center justify-center text-accent shrink-0 border border-orange-100/50">
                            <span class="text-[10px] font-bold uppercase text-orange-400/80 mb-0.5">Out</span>
                            <span class="text-lg font-bold leading-none">15</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 text-sm">Vacina V10</h4>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Max</p>
                        </div>
                        <div class="w-2 h-2 rounded-full bg-accent shrink-0"></div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center space-x-3 cursor-pointer hover:bg-slate-100 transition">
                        <div class="w-12 h-12 rounded-lg bg-white border border-slate-100 flex flex-col items-center justify-center text-slate-500 shrink-0">
                            <span class="text-[10px] font-bold uppercase text-slate-400 mb-0.5">Out</span>
                            <span class="text-lg font-bold leading-none">20</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 text-sm">Pesagem</h4>
                            <p class="text-xs font-medium text-slate-500 mt-0.5">Max</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('portal.agendamentos') }}" class="block text-center w-full mt-4 py-2.5 rounded-lg font-medium text-slate-500 hover:text-slate-700 hover:bg-slate-50 transition border border-dashed border-slate-200 text-sm cursor-pointer">
                    Ver Calendário
                </a>
            </div>

        </div>
    </div>

    <!-- Add Pet Modal -->
    @if($showAddPetModal)
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-800 text-lg">Novo Pet</h3>
                <button wire:click="$set('showAddPetModal', false)" class="text-slate-400 hover:text-red-500 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            @if(!$this->canAddPet)
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-orange-50 border border-orange-100 rounded-full flex items-center justify-center text-orange-500 mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h4 class="font-bold text-slate-800 text-xl mb-2">Limite Atingido!</h4>
                    <p class="text-slate-500 font-medium text-sm mb-6">O seu plano atual permite apenas um número limitado de pets. Para adicionar mais membros da família, você precisa fazer um upgrade de plano.</p>
                    <a href="{{ route('home') }}#planos" class="block w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-md shadow-orange-500/20">
                        Ver Planos e Fazer Upgrade
                    </a>
                </div>
            @else
            <form wire:submit="savePet" class="p-6 space-y-4">
                
                <!-- Foto Upload -->
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Foto do Pet (opcional)</label>
                        <input wire:model="photo" type="file" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-primary hover:file:bg-teal-100 transition cursor-pointer">
                        <div wire:loading wire:target="photo" class="text-xs text-primary mt-1">Processando imagem...</div>
                        @error('photo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nome do Pet *</label>
                    <input wire:model="name" type="text" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-primary focus:border-primary px-3 py-2 text-sm" required placeholder="Ex: Max">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Espécie</label>
                        <select wire:model="species" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-primary focus:border-primary px-3 py-2 text-sm">
                            <option value="">Selecione</option>
                            <option value="Cachorro">Cachorro</option>
                            <option value="Gato">Gato</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Gênero</label>
                        <select wire:model="gender" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-primary focus:border-primary px-3 py-2 text-sm">
                            <option value="">Selecione</option>
                            <option value="Macho">Macho</option>
                            <option value="Fêmea">Fêmea</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Raça</label>
                        <input wire:model="breed" type="text" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-primary focus:border-primary px-3 py-2 text-sm" placeholder="Ex: Poodle">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Cor / Pelagem</label>
                        <input wire:model="coat_color" type="text" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-primary focus:border-primary px-3 py-2 text-sm" placeholder="Ex: Branco">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Data de Nascimento</label>
                    <input wire:model="birth_date" type="date" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-primary focus:border-primary px-3 py-2 text-sm">
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" wire:click="$set('showAddPetModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-teal-700 rounded-lg shadow-sm transition flex items-center cursor-pointer">
                        <span wire:loading.remove wire:target="savePet">Salvar Pet</span>
                        <span wire:loading wire:target="savePet">Salvando...</span>
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
    @endif

    <!-- Registrar Peso Modal -->
    @if($showWeightModal)
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-800 text-lg">Registrar Peso</h3>
                <button wire:click="$set('showWeightModal', false)" class="text-slate-400 hover:text-red-500 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form wire:submit="saveWeight" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Qual Pet?</label>
                    <select wire:model="weight_pet_id" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-primary focus:border-primary px-3 py-2 text-sm" required>
                        <option value="">Selecione o pet</option>
                        @foreach(Auth::user()->pets as $pet)
                            <option value="{{ $pet->id }}">{{ $pet->name }}</option>
                        @endforeach
                    </select>
                    @error('weight_pet_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Peso (kg) *</label>
                    <input wire:model="weight_value" type="number" step="0.01" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-primary focus:border-primary px-3 py-2 text-sm" required placeholder="Ex: 15.5">
                    @error('weight_value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Data da Pesagem</label>
                    <input wire:model="weight_recorded_at" type="date" class="w-full border-slate-200 rounded-lg shadow-sm focus:ring-primary focus:border-primary px-3 py-2 text-sm" required>
                    @error('weight_recorded_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex justify-end space-x-3">
                    <button type="button" wire:click="$set('showWeightModal', false)" class="px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-primary hover:bg-teal-700 rounded-lg shadow-sm transition flex items-center cursor-pointer">
                        <span wire:loading.remove wire:target="saveWeight">Salvar Peso</span>
                        <span wire:loading wire:target="saveWeight">Salvando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
