# 🐾 AjudaPet - Portal do Tutor

O **AjudaPet** é uma plataforma dedicada a facilitar a vida de tutores de animais de estimação. Nosso objetivo é centralizar todas as informações de saúde e histórico clínico dos pets em um único lugar, garantindo organização, praticidade e bem-estar para os animais.

E-mail: test@example.com Senha: password

## Basta fazer o login com esses dados!

## 🎯 O que é o projeto?

Muitas vezes, tutores perdem as carteirinhas de vacinação de papel ou esquecem onde guardaram os resultados de exames antigos. O AjudaPet resolve esse problema oferecendo um **Portal do Tutor** digital, seguro e de fácil acesso, onde é possível cadastrar cada pet e acompanhar toda a sua trajetória de vida e saúde.

## ✨ Funcionalidades Principais (MVP)

- [x] **🐾 Gestão de Pets:** Cadastro completo com foto, raça, espécie, data de nascimento e pelagem.
- [x] **⚖️ Controle de Peso:** Registro histórico de pesagens para acompanhamento do desenvolvimento.
- [x] **💉 Carteira de Vacinação Digital:** Controle de vacinas aplicadas e futuras, com datas e lotes.
- [x] **🔬 Histórico de Exames:** Registro de exames realizados (sangue, imagem, etc).
- [x] **📂 Upload de Documentos:** Possibilidade de anexar os arquivos PDF (ou imagens) dos laudos de exames diretamente no perfil do pet.

## 🚀 Próximos Passos & Melhorias Sugeridas (Roadmap)

### ✅ Finalizados (Funcionalidades SaaS Premium)
- [x] **Identidade Digital (QR Code):** Geração de uma página pública mobile-first (Cartão Virtual) para uso em coleiras, contendo alerta médico e contato direto no WhatsApp.
- [x] **Compartilhamento Médico (PDF):** Geração de um prontuário médico em PDF contendo linha do tempo clínica, histórico de vacinas e pesagens.
- [x] **Integração de Pagamentos Asaas:** Criação de Checkout de Planos (Grátis, Pro, Max) com paywall bloqueando a criação de pets conforme o limite do plano contratado.
- [x] **Interface Mobile-First:** Portal 100% responsivo com menu lateral expansível e tabelas fluidas utilizando Tailwind e AlpineJS.
- [x] **Timeline Clínica:** Linha do tempo visual com os eventos de saúde, incluindo diários de observações e anexos de exames.
- [x] **Gráficos de Saúde:** Visualização gráfica da evolução do peso do pet ao longo do tempo.

### 🚧 O que falta terminar (Backlog)
- [ ] **Webhook Asaas Automático:** Criar o endpoint de API (`/api/webhook/asaas`) para receber o POST de confirmação de pagamento e mudar o plano de `PENDING` para `ACTIVE` instantaneamente.
- [ ] **Sistema de Alertas (E-mails):** CRON Job diário para avisar o tutor que uma vacina vence em 5 dias ou já passou do prazo.
- [ ] **Painel Admin Geral:** Um painel (talvez via Filament) onde o dono da plataforma possa ver assinaturas ativas, MRR e dados de todos os tutores cadastrados.

## 🛠️ Tecnologias Utilizadas

Este projeto está sendo construído com as seguintes tecnologias:

- **Framework:** [Laravel 11+](https://laravel.com/)
- **Frontend / Componentes:** [Livewire 3](https://livewire.laravel.com/) + [Volt](https://livewire.laravel.com/docs/volt)
- **Banco de Dados:** MySQL / SQLite
- **Estilização:** Tailwind CSS (a definir)

## ⚙️ Como rodar o projeto localmente

1. Clone o repositório.
2. Instale as dependências do PHP com o Composer:
    ```bash
    composer install
    ```
3. Instale as dependências do Node (caso esteja usando Vite/Tailwind):
    ```bash
    npm install && npm run build
    ```
4. Copie o arquivo de ambiente e gere a chave:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
5. Configure seu banco de dados no arquivo `.env` e rode as migrations:
    ```bash
    php artisan migrate
    ```
6. Inicie o servidor de desenvolvimento:
    ```bash
    php artisan serve
    ```
    _E não esqueça de rodar o Vite em outro terminal caso precise compilar assets:_ `npm run dev`
