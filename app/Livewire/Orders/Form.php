<?php

namespace App\Livewire\Orders;

use App\Models\Client;
use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?int $orderId = null;

    public ?int $client_id = null;
    public string $status = 'pending';
    public string $payment_status = 'pending';
    public ?string $delivery_date = null;
    public array $items = [];

    public function mount(?int $orderId = null): void
    {
        if ($orderId) {
            $this->orderId = $orderId;
            $order = Order::with('items')->findOrFail($orderId);
            $this->client_id = $order->client_id;
            $this->status = $order->status;
            $this->payment_status = $order->payment_status;
            $this->delivery_date = $order->delivery_date?->format('Y-m-d');
            $this->items = $order->items->map(fn ($item) => [
                'product_name' => $item->product_name,
                'description' => $item->description ?? '',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ])->toArray();
        }

        if (empty($this->items)) {
            $this->addItem();
        }
    }

    public function addItem(): void
    {
        $this->items[] = [
            'product_name' => '',
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0,
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        if (empty($this->items)) {
            $this->addItem();
        }
    }

    public function save(): void
    {
        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'status' => 'required|in:pending,paid,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,partial',
            'delivery_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string|max:500',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $totalPrice = collect($this->items)->sum(fn ($i) => $i['quantity'] * $i['unit_price']);

        $orderData = [
            'client_id' => $this->client_id,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'delivery_date' => $this->delivery_date ?: null,
            'total_price' => $totalPrice,
        ];

        if ($this->orderId) {
            $order = Order::findOrFail($this->orderId);
            $order->update($orderData);
            $order->items()->delete();
        } else {
            $order = Order::create($orderData);
        }

        foreach ($this->items as $item) {
            $order->items()->create([
                'product_name' => $item['product_name'],
                'description' => $item['description'] ?: null,
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);
        }

        session()->flash('success', $this->orderId ? 'Pedido atualizado.' : 'Pedido criado.');
        $this->redirect(route('orders.show', $order->id), navigate: true);
    }

    public function render()
    {
        $clients = Client::orderBy('first_name')->get();
        return view('livewire.orders.form', compact('clients'));
    }
}
