<?php

namespace App\Ai\Tools;

use App\Models\Client;
use App\Models\Order;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class UpdateOrder implements Tool
{
    public function description(): Stringable|string
    {
        return 'Atualiza pedido: pagamento (amount_paid+payment_method), status (pending/ready/delivered/cancelled), entrega.';
    }

    public function handle(Request $request): Stringable|string
    {
        $order = null;

        if ($request['order_id'] ?? null) {
            $order = Order::with('client')->find($request['order_id']);
        }

        if (! $order && ($request['client_name'] ?? null)) {
            $names = explode(' ', $request['client_name'], 2);
            $firstName = $names[0];
            $lastName = $names[1] ?? null;

            $clientQuery = Client::where('first_name', 'like', "%{$firstName}%");
            if ($lastName) {
                $clientQuery->where('last_name', 'like', "%{$lastName}%");
            }
            $client = $clientQuery->first();

            if ($client) {
                if ($lastName && ! $client->last_name) {
                    $client->update(['last_name' => $lastName]);
                }

                $order = Order::with('client')
                    ->where('client_id', $client->id)
                    ->whereIn('status', ['pending', 'ready'])
                    ->latest()
                    ->first();
            }
        }

        if (! $order) {
            return 'Pedido não encontrado.';
        }

        if ($request['amount_paid'] ?? null) {
            $amount = (float) $request['amount_paid'];
            $method = $request['payment_method'] ?? 'outro';
            $notes = $request['payment_notes'] ?? null;

            $order->addPayment($amount, $method, $notes);
        }

        $updates = [];

        if ($request['status'] ?? null) {
            $updates['status'] = $request['status'];
        }

        if ($request['delivery_date'] ?? null) {
            $updates['delivery_date'] = CreateOrder::parseDate($request['delivery_date']);
        }

        if (! empty($updates)) {
            $order->update($updates);
        }

        $order->refresh();

        $remaining = $order->remaining_amount;

        return "#{$order->id} {$order->client->full_name} R\${$order->total_price} pago:R\${$order->amount_paid} resta:R\${$remaining} status:{$order->status} entrega:{$order->delivery_date?->format('d/m')}";
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'order_id' => $schema->integer(),
            'client_name' => $schema->string(),
            'amount_paid' => $schema->number(),
            'payment_method' => $schema->string(),
            'payment_notes' => $schema->string(),
            'payment_status' => $schema->string(),
            'status' => $schema->string(),
            'delivery_date' => $schema->string(),
        ];
    }
}
