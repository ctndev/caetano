<?php

namespace App\Ai\Tools;

use App\Models\Client;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CreateOrder implements Tool
{
    public function description(): Stringable|string
    {
        return 'Cria pedido de venda com cliente, itens, preços e entrega.';
    }

    public function handle(Request $request): Stringable|string
    {
        $client = Client::where('first_name', 'like', $request['client_first_name'])
            ->when($request['client_last_name'] ?? null, fn ($q, $ln) => $q->where('last_name', 'like', $ln))
            ->first();

        $needsLastName = false;

        if (! $client) {
            $client = Client::create([
                'first_name' => $request['client_first_name'],
                'last_name' => $request['client_last_name'] ?? null,
            ]);
            if (empty($request['client_last_name'])) {
                $needsLastName = true;
            }
        }

        $items = $request['items'] ?? [];
        $totalPrice = $request['total_price'] ?? collect($items)->sum(fn ($i) => ($i['quantity'] ?? 1) * ($i['unit_price'] ?? 0));

        $order = Order::create([
            'client_id' => $client->id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'total_price' => $totalPrice,
            'delivery_date' => self::parseDate($request['delivery_date'] ?? null),
        ]);

        foreach ($items as $item) {
            $order->items()->create([
                'product_name' => $item['product_name'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
            ]);
        }

        $msg = "Pedido #{$order->id} criado, {$client->full_name}, R\${$totalPrice}";
        if ($needsLastName) {
            $msg .= ' (precisa sobrenome)';
        }

        return $msg;
    }

    public static function parseDate(?string $date): ?Carbon
    {
        if (! $date) {
            return null;
        }

        $formats = ['d/m/Y', 'd/m', 'Y-m-d', 'd-m-Y', 'd-m'];

        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, trim($date));

                if (! str_contains($format, 'Y')) {
                    $parsed->year(now()->year);
                    if ($parsed->isPast() && $parsed->diffInMonths(now()) > 6) {
                        $parsed->addYear();
                    }
                }

                return $parsed->startOfDay();
            } catch (\Exception) {
                continue;
            }
        }

        try {
            return Carbon::parse($date)->startOfDay();
        } catch (\Exception) {
            return null;
        }
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'client_first_name' => $schema->string()->required(),
            'client_last_name' => $schema->string(),
            'total_price' => $schema->number(),
            'delivery_date' => $schema->string(),
            'items' => $schema->array()->items(
                $schema->object([
                    'product_name' => $schema->string()->required(),
                    'description' => $schema->string(),
                    'quantity' => $schema->integer(),
                    'unit_price' => $schema->number(),
                ])
            )->required(),
        ];
    }
}
