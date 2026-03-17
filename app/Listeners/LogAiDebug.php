<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Events\AgentPrompted;
use Laravel\Ai\Events\PromptingAgent;
use Laravel\Ai\Messages\AssistantMessage;

class LogAiDebug
{
    private bool $promptLogged = false;

    private bool $responseLogged = false;

    public function handlePrompting(PromptingAgent $event): void
    {
        if ($this->promptLogged) {
            return;
        }
        $this->promptLogged = true;

        $prompt = $event->prompt;
        $agent = $prompt->agent;

        $contextMessages = [];
        if ($agent instanceof Conversational) {
            foreach ($agent->messages() as $msg) {
                $entry = ['role' => $msg->role->value, 'len' => strlen($msg->content ?? '')];

                if ($msg instanceof AssistantMessage && $msg->toolCalls->isNotEmpty()) {
                    $entry['tools'] = $msg->toolCalls->map(fn ($tc) => $tc->name ?? '?')->values()->all();
                }

                if (strlen($msg->content ?? '') <= 200) {
                    $entry['text'] = $msg->content;
                } else {
                    $entry['text'] = substr($msg->content, 0, 200) . '...';
                }

                $contextMessages[] = $entry;
            }
        }

        Log::channel('ai')->info('>>> AI REQUEST', [
            'model' => $prompt->model,
            'prompt' => $prompt->prompt,
            'instructions_len' => strlen((string) $agent->instructions()),
            'context_count' => count($contextMessages),
            'context' => $contextMessages,
        ]);
    }

    public function handlePrompted(AgentPrompted $event): void
    {
        if ($this->responseLogged) {
            return;
        }
        $this->responseLogged = true;

        $response = $event->response;
        $usage = $response->usage;

        $steps = [];
        foreach ($response->steps as $i => $step) {
            $s = [
                'n' => $i + 1,
                'reason' => $step->finishReason->value,
                'p_tok' => $step->usage->promptTokens,
                'c_tok' => $step->usage->completionTokens,
            ];

            if (! empty($step->toolCalls)) {
                $s['calls'] = collect($step->toolCalls)->map(fn ($tc) => [
                    'tool' => $tc->name,
                    'args' => $tc->arguments,
                ])->all();
            }

            if (! empty($step->toolResults)) {
                $s['results'] = collect($step->toolResults)->map(fn ($tr) => [
                    'tool' => $tr->name,
                    'len' => strlen(is_string($tr->result) ? $tr->result : json_encode($tr->result)),
                    'data' => is_string($tr->result) ? $tr->result : json_encode($tr->result),
                ])->all();
            }

            $steps[] = $s;
        }

        Log::channel('ai')->info('<<< AI RESPONSE', [
            'model' => $event->prompt->model,
            'text' => $response->text,
            'p_tok' => $usage->promptTokens,
            'c_tok' => $usage->completionTokens,
            'total' => $usage->promptTokens + $usage->completionTokens,
            'steps' => $steps,
        ]);
    }
}
