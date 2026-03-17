<?php

namespace App\Ai\Tools;

use App\Models\Client;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class QueryClients implements Tool
{
    public function description(): Stringable|string
    {
        return 'Busca clientes por nome (parcial) ou lista todos.';
    }

    public function handle(Request $request): Stringable|string
    {
        $query = Client::withCount('orders');

        if ($name = $request['name'] ?? null) {
            $parts = preg_split('/\s+/', trim($name));

            $query->where(function ($q) use ($parts) {
                foreach ($parts as $part) {
                    $q->where(function ($q2) use ($part) {
                        $q2->where('first_name', 'like', "%{$part}%")
                           ->orWhere('last_name', 'like', "%{$part}%");
                    });
                }
            });
        }

        $clients = $query->latest()->limit(20)->get();

        $lines = $clients->map(fn ($c) => "#{$c->id} {$c->full_name} pedidos:{$c->orders_count}");

        return "{$clients->count()} clientes\n" . $lines->implode("\n");
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string(),
        ];
    }
}
