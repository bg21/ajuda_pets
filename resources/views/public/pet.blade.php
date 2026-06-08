<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Público - {{ $pet->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #DB2777; /* Rosa para destaque na identidade */
            --bg: #FDF2F8;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            margin: 0;
            padding: 0;
            color: #191919;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .card-container {
            width: 100%;
            max-width: 450px;
            background: #fff;
            min-height: 100vh;
            box-shadow: 0 0 40px rgba(0,0,0,0.05);
            position: relative;
            padding-bottom: 40px;
        }
        .cover {
            height: 200px;
            background: linear-gradient(135deg, #DB2777, #9D174D);
            position: relative;
        }
        .photo {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: #fff;
            position: absolute;
            bottom: -70px;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid #fff;
            box-shadow: 0 10px 25px rgba(219, 39, 119, 0.2);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            font-weight: 800;
            color: #DB2777;
        }
        .photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .content {
            padding: 90px 24px 24px 24px;
            text-align: center;
        }
        .name {
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 5px 0;
            letter-spacing: -1px;
        }
        .breed {
            font-size: 16px;
            color: #64748B;
            font-weight: 500;
            margin: 0 0 20px 0;
        }
        .grid-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }
        .info-box {
            background: #F8FAFC;
            padding: 16px;
            border-radius: 16px;
            border: 1px solid #F1F5F9;
            text-align: left;
        }
        .info-label {
            font-size: 12px;
            color: #94A3B8;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 15px;
            font-weight: 700;
            color: #334155;
        }
        .alert-box {
            background: #FEF2F2;
            border: 1px solid #FCA5A5;
            padding: 20px;
            border-radius: 16px;
            text-align: left;
            margin-bottom: 24px;
        }
        .alert-title {
            color: #DC2626;
            font-weight: 800;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .alert-text {
            color: #991B1B;
            font-size: 15px;
            line-height: 1.5;
            font-weight: 500;
            margin: 0;
        }
        .btn-whatsapp {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #25D366;
            color: #fff;
            text-decoration: none;
            padding: 18px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 16px;
            box-shadow: 0 10px 20px rgba(37, 211, 102, 0.2);
            transition: 0.2s;
        }
        .btn-whatsapp:active {
            transform: scale(0.98);
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: #94A3B8;
            font-weight: 500;
        }
        .footer strong {
            color: #DB2777;
        }
    </style>
</head>
<body>

    <div class="card-container">
        <div class="cover">
            <div class="photo">
                @if($pet->photo_path)
                    <img src="{{ Storage::url($pet->photo_path) }}" alt="{{ $pet->name }}">
                @else
                    {{ substr($pet->name, 0, 1) }}
                @endif
            </div>
        </div>

        <div class="content">
            <h1 class="name">Eu sou o {{ $pet->name }}</h1>
            <p class="breed">{{ ucfirst($pet->species ?? 'Pet') }} • {{ $pet->breed ?: 'Sem raça' }}</p>

            <div class="grid-info">
                <div class="info-box">
                    <div class="info-label">Gênero</div>
                    <div class="info-value">{{ ucfirst($pet->gender ?? 'N/A') }}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">Idade</div>
                    <div class="info-value">{{ $pet->birth_date ? \Carbon\Carbon::parse($pet->birth_date)->age . ' anos' : 'N/A' }}</div>
                </div>
                <div class="info-box" style="grid-column: span 2;">
                    <div class="info-label">Cor / Pelagem</div>
                    <div class="info-value">{{ $pet->coat_color ?: 'Não informada' }}</div>
                </div>
            </div>

            @if($pet->medical_conditions)
            <div class="alert-box">
                <div class="alert-title">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Atenção Médica
                </div>
                <p class="alert-text">{{ $pet->medical_conditions }}</p>
            </div>
            @endif

            <div style="margin-top: 32px;">
                <p style="color: #64748B; font-size: 14px; font-weight: 600; margin-bottom: 12px;">Me encontrou perdido? Entre em contato com meu tutor:</p>
                
                @if($pet->emergency_contact)
                    @php
                        // Limpa o numero para gerar o link do whatsapp
                        $cleanPhone = preg_replace('/[^0-9]/', '', $pet->emergency_contact);
                        // Adiciona 55 se nao tiver
                        if(strlen($cleanPhone) <= 11) {
                            $cleanPhone = '55' . $cleanPhone;
                        }
                    @endphp
                    <a href="https://wa.me/{{ $cleanPhone }}?text=Olá!%20Encontrei%20o%20seu%20pet%20{{ urlencode($pet->name) }}." class="btn-whatsapp">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.031 22c-1.724 0-3.413-.451-4.896-1.305l-5.46 1.433 1.458-5.321c-.934-1.528-1.427-3.29-1.427-5.086 0-5.514 4.486-10 10-10s10 4.486 10 10-4.486 10-10 10z"/></svg>
                        Avisar no WhatsApp
                    </a>
                @else
                    <div style="background: #F1F5F9; color: #64748B; padding: 16px; border-radius: 12px; font-weight: 600;">
                        O tutor não cadastrou um contato de emergência.
                    </div>
                @endif
            </div>

            <div class="footer">
                Identidade Digital provida por <strong>AjudaPet</strong>
            </div>
        </div>
    </div>

</body>
</html>
