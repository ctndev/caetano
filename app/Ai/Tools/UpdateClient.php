<?php

namespace App\Ai\Tools;

use App\Models\Client;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class UpdateClient implements Tool
{
    public function description(): Stringable|string
    {
        return 'Atualiza dados de um cliente existente: nome, sobrenome, telefone, email. Também pode excluir (delete=true).';
    }

    public function handle(Request $request): Stringable|string
    {
        if (! ($request['client_id'] ?? null) && ! ($request['search_name'] ?? null)) {
            return 'Informe o ID ou nome do cliente.';
        }

        $client = null;

        if ($request['client_id'] ?? null) {
            $client = Client::find($request['client_id']);
        }

        if (! $client && ($request['search_name'] ?? null)) {
            $parts = preg_split('/\s+/', trim($request['search_name']));

            $query = Client::query();
            foreach ($parts as $part) {
                $query->where(function ($q) use ($part) {
                    $q->where('first_name', 'like', "%{$part}%")
                      ->orWhere('last_name', 'like', "%{$part}%");
                });
            }
            $client = $query->first();
        }

        if (! $client) {
            return 'Cliente não encontrado.';
        }

        if ($request['delete'] ?? false) {
            if ($client->orders()->exists()) {
                return "Cliente {$client->full_name} possui pedidos e não pode ser excluído.";
            }

            $name = $client->full_name;
            $client->delete();

            return "Cliente {$name} removido.";
        }

        $updates = [];
        if ($request['first_name'] ?? null) {
            $updates['first_name'] = $request['first_name'];
        }
        if ($request['last_name'] ?? null) {
            $updates['last_name'] = $request['last_name'];
        }
        if (array_key_exists('phone', $request->all())) {
            $updates['phone'] = $request['phone'];
        }
        if (array_key_exists('email', $request->all())) {
            $updates['email'] = $request['email'];
        }

        if (! empty($updates)) {
            $client->update($updates);
            $client->refresh();
        }

        return "#{$client->id} {$client->full_name} atualizado.";
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'client_id' => $schema->integer(),
            'search_name' => $schema->string(),
            'first_name' => $schema->string(),
            'last_name' => $schema->string(),
            'phone' => $schema->string(),
            'email' => $schema->string(),
            'delete' => $schema->boolean(),
        ];
    }
}
