# Caetano - Sistema de Vendas via WhatsApp

Sistema de gerenciamento de vendas integrado com WhatsApp, utilizando inteligência artificial para processar pedidos via mensagens de texto e áudio.

## Arquitetura

```
WhatsApp User  →  WPPConnect (Node.js)  →  Laravel API  →  OpenAI GPT-4.1
                                            ↓
                                         SQLite DB
                                            ↓
                                      Livewire Dashboard
```

**Backend:** Laravel 12, PHP 8.2+, SQLite
**Frontend:** Livewire 3, Tailwind CSS 4, Alpine.js
**AI:** Laravel AI SDK + OpenAI GPT-4.1
**WhatsApp:** WPPConnect (Node.js) em `server-node/`
**Áudio:** Transcrição via OpenAI Whisper (Laravel AI SDK)

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- npm
- Chromium ou Google Chrome (para WPPConnect/Puppeteer)

## Instalação

### 1. Laravel (Backend)

```bash
# Instalar dependências PHP
composer install

# Copiar .env e gerar chave
cp .env.example .env
php artisan key:generate

# Rodar migrations
php artisan migrate

# Instalar dependências front-end e compilar
npm install
npm run build
```

### 2. Server Node (WhatsApp Bot)

```bash
cd server-node

# Copiar .env
cp .env.example .env

# Configurar variáveis (API_SECRET deve ser igual no Laravel e Node)
# Editar .env com seu editor

# Instalar dependências
npm install
```

### 3. Configurar variáveis de ambiente

No `.env` do Laravel:

| Variável | Descrição |
|----------|-----------|
| `OPENAI_API_KEY` | Chave da API OpenAI |
| `WPPCONNECT_URL` | URL do servidor Node (padrão: `http://localhost:3001`) |
| `WPPCONNECT_SECRET` | Segredo compartilhado para autenticação API |

No `.env` do server-node:

| Variável | Descrição |
|----------|-----------|
| `PORT` | Porta do servidor (padrão: `3001`) |
| `LARAVEL_API_URL` | URL da API Laravel (padrão: `http://localhost:8000/api`) |
| `API_SECRET` | Mesmo segredo do `WPPCONNECT_SECRET` do Laravel |

## Executando

### Desenvolvimento

```bash
# Terminal 1 - Laravel
composer dev

# Terminal 2 - WhatsApp Bot
cd server-node
npm start
```

Na primeira execução do bot, um QR Code será exibido no terminal. Escaneie com o WhatsApp para conectar. A sessão é persistente (não precisa escanear novamente).

### Produção

Para o bot Node.js, use PM2:

```bash
npm install -g pm2
cd server-node
pm2 start src/index.js --name caetano-bot
pm2 startup
pm2 save
```

## Banco de Dados

### Tabelas

| Tabela | Descrição |
|--------|-----------|
| `users` | Usuários do sistema (admin) |
| `clients` | Clientes cadastrados |
| `orders` | Pedidos de venda |
| `order_items` | Itens de cada pedido |
| `allowed_numbers` | Números autorizados para interagir com o bot |
| `messages` | Histórico de mensagens WhatsApp |

### Status de Pedido

- `pending` - Pendente
- `paid` - Pago
- `delivered` - Entregue
- `cancelled` - Cancelado

### Status de Pagamento

- `pending` - Pendente
- `paid` - Pago
- `partial` - Parcial

## API Endpoints

Todos protegidos pelo header `X-Api-Secret`.

| Método | Rota | Descrição |
|--------|------|-----------|
| `POST` | `/api/whatsapp/message` | Receber mensagem do bot (texto ou áudio) |
| `GET` | `/api/whatsapp/status` | Status da conexão WhatsApp |
| `GET` | `/api/whatsapp/qrcode` | QR Code para conexão |

### POST /api/whatsapp/message

Corpo da requisição:

```json
{
    "number": "5585999999999",
    "message": "Vendi duas canecas para o João por 80 reais",
    "type": "text"
}
```

Para áudio, enviar como `multipart/form-data` com campo `audio` (arquivo).

Resposta:

```json
{
    "reply": "Pedido #1 criado! 2 canecas para João, total R$80,00. Qual o sobrenome do João?"
}
```

## AI Agent - Ferramentas

O agente `SalesAssistant` possui as seguintes ferramentas:

| Ferramenta | Descrição |
|------------|-----------|
| `CreateOrder` | Cria pedido com cliente, itens, preços e data de entrega |
| `UpdateOrder` | Atualiza status de pagamento, entrega e dados do pedido |
| `QueryOrders` | Consulta pedidos (abertos, do mês, por cliente, etc.) |
| `CreateClient` | Cria ou atualiza cliente |
| `QueryClients` | Busca clientes cadastrados |

### Exemplos de Uso via WhatsApp

**Criar pedido:**
> "Vendi duas canecas para o João por 80 reais. Uma com foto de coelho e outra com foto de gato. Para entregar até dia 30/03."

**Atualizar pagamento:**
> "João da Silva pagou as duas canecas, 80 reais, falta entregar."

**Consultar vendas:**
> "Quantas vendas estão em aberto?"
> "Quantas vendas fiz este mês?"

**Enviar áudio:**
> Grave um áudio com o pedido e envie - o sistema transcreve e processa automaticamente.

## Telas do Sistema

- **Dashboard** - KPIs: pedidos abertos, vendas do mês, faturamento, total de clientes, status do bot
- **Clientes** - CRUD completo com busca
- **Pedidos** - CRUD com itens, filtro por status/pagamento
- **Mensagens** - Histórico de mensagens com filtros
- **Números Permitidos** - Gerenciar números autorizados
- **Status do Bot** - Conexão WhatsApp, QR Code, métricas

## Segurança

- Autenticação obrigatória para todas as telas
- API protegida por segredo compartilhado (`X-Api-Secret`)
- Apenas números cadastrados em "Números Permitidos" podem interagir com o bot
- CSRF protection em todas as rotas web
- A IA nunca executa SQL diretamente - usa ferramentas (Tools) que executam queries via Eloquent
- Validação de entrada em todos os formulários e endpoints
- Header `X-Powered-By` desabilitado no Express
- Senhas armazenadas com bcrypt (12 rounds)

## Estrutura de Diretórios

```
caetano/
├── app/
│   ├── Ai/
│   │   ├── Agents/SalesAssistant.php     # Agente IA principal
│   │   └── Tools/                         # Ferramentas do agente
│   ├── Http/
│   │   ├── Controllers/Api/               # Controller da API WhatsApp
│   │   └── Middleware/                     # Validação de segredo API
│   ├── Livewire/                          # Componentes Livewire
│   ├── Models/                            # Eloquent models
│   └── Services/WppConnectService.php     # Comunicação com Node.js
├── database/migrations/                   # Migrations do banco
├── resources/views/
│   ├── layouts/                           # Layouts (app + guest)
│   └── livewire/                          # Views dos componentes
├── routes/
│   ├── web.php                            # Rotas web (Livewire)
│   └── api.php                            # Rotas API (WhatsApp)
├── server-node/                           # Bot WhatsApp (Node.js)
│   └── src/
│       ├── index.js                       # Entry point
│       ├── services/whatsapp.js           # WPPConnect client
│       ├── services/laravel.js            # HTTP client para Laravel
│       ├── routes/                        # Rotas Express
│       └── middleware/auth.js             # Autenticação API
└── config/ai.php                          # Configuração AI SDK
```

## Troubleshooting

**Bot não conecta:**
- Verifique se o Node.js está rodando (`npm start` no `server-node/`)
- Verifique se o Chromium está instalado
- Verifique os logs do terminal

**QR Code não aparece no dashboard:**
- Confirme que `WPPCONNECT_URL` e `WPPCONNECT_SECRET` estão corretos
- Verifique se o server-node está acessível na URL configurada

**Mensagens não são processadas:**
- Verifique se `OPENAI_API_KEY` está configurada
- Verifique se o número está cadastrado em "Números Permitidos"
- Verifique os logs do Laravel (`storage/logs/laravel.log`)

**Áudio não funciona:**
- O formato OGG do WhatsApp é suportado pela OpenAI
- Verifique se o arquivo não excede 25MB
- Verifique os logs para erros de transcrição
