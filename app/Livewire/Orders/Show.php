<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Order $order;

    public function mount(int $orderId): void
    {
        $this->order = Order::with(['client', 'items'])->findOrFail($orderId);
    }

    public function updateStatus(string $status): void
    {
        $this->order->update(['status' => $status]);
        $this->order->refresh();
        session()->flash('success', 'Status atualizado.');
    }

    public function updatePaymentStatus(string $paymentStatus): void
    {
        $this->order->update(['payment_status' => $paymentStatus]);
        if ($paymentStatus === 'paid' && $this->order->status === 'pending') {
            $this->order->update(['status' => 'paid']);
        }
        $this->order->refresh();
        session()->flash('success', 'Status de pagamento atualizado.');
    }

    public function render()
    {
        return view('livewire.orders.show');
    }
}
