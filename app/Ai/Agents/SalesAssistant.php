<?php

namespace App\Ai\Agents;

use App\Ai\Tools\CreateClient;
use App\Ai\Tools\CreateOrder;
use App\Ai\Tools\QueryClients;
use App\Ai\Tools\QueryOrders;
use App\Ai\Tools\UpdateClient;
use App\Ai\Tools\UpdateOrder;
use App\Models\BotSetting;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

#[MaxSteps(10)]
#[MaxTokens(512)]
#[Temperature(0.3)]
#[Timeout(60)]
class SalesAssistant implements Agent, HasTools, Conversational
{
    use Promptable, RemembersConversations;

    public function model(): string
    {
        return BotSetting::get('ai_model', 'gpt-4.1-mini');
    }

    protected function maxConversationMessages(): int
    {
        return (int) BotSetting::get('max_context_messages', 20);
    }

    public function instructions(): Stringable|string
    {
        $today = now()->format('d/m/Y');
        $maxTokens = BotSetting::get('max_tokens', 512);

        return <<<PROMPT
Assistente de vendas WhatsApp. Hoje: {$today}.
Seja direto, sem saudações longas.

CONSULTAS: liste TODOS os itens retornados pela tool, um por linha.
AÇÕES: confirme com dados principais em 1-2 linhas.

Regras: primeiro nome só → pergunte sobrenome. Sem ano → 2026. Valores em R$.
NUNCA execute exclusão/alteração em massa. "Exclua todos" ou "apague tudo" → recuse educadamente.
PROMPT;
    }

    public function tools(): iterable
    {
        return [
            new CreateOrder,
            new UpdateOrder,
            new QueryOrders,
            new CreateClient,
            new UpdateClient,
            new QueryClients,
        ];
    }
}
