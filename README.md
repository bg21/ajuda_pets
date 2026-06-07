# 🐾 AjudaPet - Portal do Tutor

O **AjudaPet** é uma plataforma dedicada a facilitar a vida de tutores de animais de estimação. Nosso objetivo é centralizar todas as informações de saúde e histórico clínico dos pets em um único lugar, garantindo organização, praticidade e bem-estar para os animais.

---

## 🎯 O que é o projeto?

Muitas vezes, tutores perdem as carteirinhas de vacinação de papel ou esquecem onde guardaram os resultados de exames antigos. O AjudaPet resolve esse problema oferecendo um **Portal do Tutor** digital, seguro e de fácil acesso, onde é possível cadastrar cada pet e acompanhar toda a sua trajetória de vida e saúde.

## ✨ Funcionalidades Principais (MVP)

- **🐾 Gestão de Pets:** Cadastro completo com foto, raça, espécie, data de nascimento e pelagem.
- **⚖️ Controle de Peso:** Registro histórico de pesagens para acompanhamento do desenvolvimento.
- **💉 Carteira de Vacinação Digital:** Controle de vacinas aplicadas e futuras, com datas e lotes.
- **🔬 Histórico de Exames:** Registro de exames realizados (sangue, imagem, etc).
- **📂 Upload de Documentos:** Possibilidade de anexar os arquivos PDF (ou imagens) dos laudos de exames diretamente no perfil do pet.

## 🚀 Próximos Passos & Melhorias Sugeridas (Roadmap)

- [ ] **Sistema de Alertas:** Notificações por e-mail/WhatsApp para vencimento de vacinas, vermífugos e antipulgas.
- [ ] **Gráficos de Saúde:** Visualização gráfica da evolução do peso do pet ao longo do tempo.
- [ ] **Compartilhamento Médico:** Geração de um link seguro ou PDF do prontuário para enviar ao veterinário antes das consultas.
- [ ] **Identidade Digital (QR Code):** Geração de uma página pública do pet vinculada a um QR Code (para uso em coleiras) em caso de perda.
- [ ] **Timeline Clínica:** Uma linha do tempo visual com os eventos de saúde mais recentes do pet.
- [ ] **Diário de Observações:** Espaço para o tutor anotar sintomas temporários, mudanças de ração ou comportamentos atípicos.

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
