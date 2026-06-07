<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Tutor;

new #[Layout('layouts.tutor')] class extends Component {
    public $login = '';
    public $password = '';
    public $errorMessage = '';

    public function authenticate()
    {
        $this->validate([
            'login' => 'required',
            'password' => 'required'
        ]);

        $loginSanitized = preg_replace('/[^0-9]/', '', $this->login);

        // Busca o tutor ignorando a pontuação do CPF ou do Telefone que estão no banco
        $tutor = Tutor::with('clinica')
            ->whereRaw("REGEXP_REPLACE(cpf, '[^0-9]', '') = ?", [$loginSanitized])
            ->orWhereRaw("REGEXP_REPLACE(telefone, '[^0-9]', '') = ?", [$loginSanitized])
            ->first();

        if ($tutor && Hash::check($this->password, $tutor->password)) {
            // Regra de Negócio: Clínicas no plano Básico não têm acesso ao Portal do Tutor
            if ($tutor->clinica && $tutor->clinica->plano === 'basico') {
                $this->errorMessage = 'O Portal do Tutor não está ativado na clínica do seu pet. Entre em contato com o veterinário.';
                return;
            }

            Auth::guard('tutor')->login($tutor);
            request()->session()->regenerate();
            return redirect()->route('portal.dashboard');
        }

        $this->errorMessage = 'CPF/Telefone ou senha incorretos.';
    }
}; ?>

<div style="padding: 32px 24px; min-height: 100vh; display: flex; flex-direction: column; justify-content: center;">
    <div style="text-align: center; margin-bottom: 40px;">
        <div style="width: 72px; height: 72px; background: #299D91; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <svg width="40" height="40" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
            </svg>
        </div>
        <h1 style="font-size: 24px; font-weight: 800; color: #191919; margin: 0 0 8px 0; letter-spacing: -0.5px;">Portal do Tutor</h1>
        <p style="font-size: 14px; color: #878787; margin: 0;">Acompanhe a saúde do seu pet de perto</p>
    </div>

    <form wire:submit="authenticate" style="display: flex; flex-direction: column; gap: 20px;">
        
        @if($errorMessage)
            <div style="background: #FEE2E2; color: #DC2626; padding: 12px; border-radius: 12px; font-size: 13px; font-weight: 600; text-align: center;">
                {{ $errorMessage }}
            </div>
        @endif

        <div>
            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">CPF ou Telefone</label>
            <input type="text" wire:model="login" placeholder="Apenas números" style="width: 100%; border: 1px solid #EFEFEF; background: #F8F9FA; border-radius: 12px; padding: 14px 16px; font-size: 15px; color: #191919; outline: none; box-sizing: border-box; transition: all 0.2s;" onfocus="this.style.borderColor='#299D91'; this.style.background='#fff'" onblur="this.style.borderColor='#EFEFEF'; this.style.background='#F8F9FA'">
        </div>

        <div>
            <label style="display: block; font-size: 13px; font-weight: 600; color: #525256; margin-bottom: 8px;">Senha</label>
            <input type="password" wire:model="password" placeholder="Sua senha de acesso" style="width: 100%; border: 1px solid #EFEFEF; background: #F8F9FA; border-radius: 12px; padding: 14px 16px; font-size: 15px; color: #191919; outline: none; box-sizing: border-box; transition: all 0.2s;" onfocus="this.style.borderColor='#299D91'; this.style.background='#fff'" onblur="this.style.borderColor='#EFEFEF'; this.style.background='#F8F9FA'">
        </div>

        <button type="submit" style="background: #299D91; color: #fff; width: 100%; border: none; padding: 16px; border-radius: 12px; font-size: 16px; font-weight: 700; margin-top: 10px; cursor: pointer; box-shadow: 0 4px 12px rgba(41, 157, 145, 0.2);">
            <span wire:loading.remove>Entrar no Portal</span>
            <span wire:loading>Entrando...</span>
        </button>
    </form>
    
    <div style="text-align: center; margin-top: 40px; font-size: 12px; color: #9F9F9F;">
        Tecnologia VetOS
    </div>
</div>
