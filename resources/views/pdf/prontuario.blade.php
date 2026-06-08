<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Prontuário Médico - {{ $pet->name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #0F766E;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .section {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        .section-title {
            color: #0F766E;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #0F766E;
            display: inline-block;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #555;
            width: 15%;
        }
        td {
            width: 35%;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 10px;
            border: 1px solid #ccc;
        }
        .timeline-item {
            margin-bottom: 20px;
            padding-left: 15px;
            border-left: 3px solid #0F766E;
        }
        .timeline-date {
            font-weight: bold;
            color: #0F766E;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .timeline-content {
            font-size: 14px;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-top: 30px;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Prontuário Médico Digital</h1>
        <p>Gerado pelo AjudaPet em {{ now()->format('d/m/Y \à\s H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Dados do Paciente</div>
        <table>
            <tr>
                <th>Nome:</th>
                <td>{{ $pet->name }}</td>
                <th>Espécie:</th>
                <td>{{ ucfirst($pet->species ?? 'N/A') }}</td>
            </tr>
            <tr>
                <th>Raça:</th>
                <td>{{ $pet->breed ?: 'N/A' }}</td>
                <th>Sexo:</th>
                <td>{{ ucfirst($pet->gender ?? 'N/A') }}</td>
            </tr>
            <tr>
                <th>Nascimento:</th>
                <td>{{ $pet->birth_date ? \Carbon\Carbon::parse($pet->birth_date)->format('d/m/Y') : 'N/A' }}</td>
                <th>Pelagem:</th>
                <td>{{ $pet->coat_color ?: 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Timeline Clínica (Histórico Completo)</div>
        
        @if(count($timeline) > 0)
            @foreach($timeline as $event)
                <div class="timeline-item">
                    <div class="timeline-date">
                        {{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y') }}
                        
                        @if($event['type'] == 'peso')
                            <span class="badge">Registro de Peso</span>
                        @elseif($event['type'] == 'vacina')
                            <span class="badge">Vacina Aplicada</span>
                        @elseif($event['type'] == 'exame')
                            <span class="badge">Exame Realizado</span>
                        @elseif($event['type'] == 'diario')
                            <span class="badge">Diário de Observação</span>
                        @elseif($event['type'] == 'nascimento')
                            <span class="badge">Nascimento</span>
                        @endif
                    </div>
                    
                    <div class="timeline-content">
                        @if($event['type'] == 'peso')
                            Peso registrado: <strong>{{ number_format($event['data']->weight, 1, ',', '.') }} kg</strong>.
                            @if(isset($event['data']->notes) && $event['data']->notes)
                                <br>Notas: {{ $event['data']->notes }}
                            @endif
                        @elseif($event['type'] == 'vacina')
                            Vacina: <strong>{{ $event['data']->name }}</strong>
                            @if($event['data']->next_due_date)
                                <br>Próxima dose: {{ \Carbon\Carbon::parse($event['data']->next_due_date)->format('d/m/Y') }}
                            @endif
                            @if($event['data']->batch_number)
                                <br>Lote: {{ $event['data']->batch_number }}
                            @endif
                        @elseif($event['type'] == 'exame')
                            Exame: <strong>{{ $event['data']->name }}</strong>
                            @if($event['data']->notes)
                                <br>Resultado/Notas: {{ $event['data']->notes }}
                            @endif
                        @elseif($event['type'] == 'diario')
                            <strong>{{ $event['data']->title ?: 'Observação Geral' }}</strong>
                            <br>{{ $event['data']->description }}
                        @elseif($event['type'] == 'nascimento')
                            Bem-vindo ao mundo, {{ $event['data']->name }}!
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p>Nenhum evento registrado na timeline deste pet.</p>
        @endif
    </div>

    <div class="footer">
        <p>Documento gerado automaticamente pela plataforma AjudaPet.</p>
    </div>

</body>
</html>
