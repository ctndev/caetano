<?php

namespace App\Ai\Tools;

use App\Models\Client;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CreateClient implements Tool
{
    public function description(): Stringable|string
    {
        return 'Cadastra novo cliente.';
    }

    public function handle(Request $request): Stringable|string
    {
        $existing = Client::where('first_name', 'like', $request['first_name'])
            ->when($request['last_name'] ?? null, fn ($q, $ln) => $q->where('last_name', 'like', $ln))
            ->first();

        if ($existing) {
            if ($request['last_name'] && ! $existing->last_name) {
                $existing->update(['last_name' => $request['last_name']]);
            }
            if ($request['phone'] ?? null) {
                $existing->update(['phone' => $request['phone']]);
            }

            return "Já existe: #{$existing->id} {$existing->full_name}";
        }

        $client = Client::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'] ?? null,
            'phone' => $request['phone'] ?? null,
            'email' => $request['email'] ?? null,
        ]);

        return "Criado: #{$client->id} {$client->full_name}";
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'first_name' => $schema->string()->required(),
            'last_name' => $schema->string(),
            'phone' => $schema->string(),
            'email' => $schema->string(),
        ];
    }
}
