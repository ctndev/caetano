<?php

namespace App\Ai\Tools;

use App\Models\Order;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class QueryOrders implements Tool
{
    public function description(): Stringable|string
    {
        return 'Consulta pedidos. query_type: open|this_month|pending_delivery|pending_payment|by_client|all';
    }

    public function handle(Request $request): Stringable|string
    {
        $query = Order::with(['client', 'items']);
        $queryType = $request['query_type'] ?? 'open';

        switch ($queryType) {
            case 'open':
                $query->open();
                break;
            case 'this_month':
                $query->thisMonth();
                break;
            case 'pending_delivery':
                $query->where('payment_status', 'paid')->where('status', '!=', 'delivered');
                break;
            case 'pending_payment':
                $query->where('payment_status', 'pending');
                break;
            case 'by_client':
                if ($request['client_name'] ?? null) {
                    $query->whereHas('client', function ($q) use ($request) {
                        $q->where('first_name', 'like', "%{$request['client_name']}%")
                          ->orWhere('last_name', 'like', "%{$request['client_name']}%");
                    });
                }
                break;
            case 'all':
                break;
        }

        $orders = $query->latest()->limit(20)->get();

        $lines = $orders->map(function ($o) {
            $items = $o->items->map(fn ($i) => "{$i->quantity}x {$i->product_name}")->implode(', ');
            $paid = $o->amount_paid > 0 ? " pago:{$o->amount_paid}" : '';
            $st = match ($o->status) {
                'ready' => ' pronto', 'delivered' => ' entregue', 'cancelled' => ' cancelado', default => '',
            };

            return "#{$o->id} {$o->client->full_name} R\${$o->total_price}{$paid}{$st} entrega:{$o->delivery_date?->format('d/m')} [{$items}]";
        });

        $total = $orders->sum('total_price');

        return "{$orders->count()} pedidos, total R\${$total}\n" . $lines->implode("\n");
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query_type' => $schema->string()->required(),
            'client_name' => $schema->string(),
        ];
    }
}
