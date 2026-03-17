<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Message;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $openOrders = Order::open()->count();
        $monthlyOrders = Order::thisMonth()->count();
        $monthlyRevenue = Order::thisMonth()->sum('total_price');
        $totalClients = Client::count();
        $recentOrders = Order::with('client')->latest()->limit(5)->get();
        $pendingDeliveries = Order::where('status', 'paid')
            ->where('payment_status', 'paid')
            ->count();

        $botStatus = 'offline';
        try {
            $response = Http::timeout(3)->get(config('services.wppconnect.url', 'http://localhost:3001') . '/api/status');
            if ($response->ok()) {
                $botStatus = $response->json('status', 'offline');
            }
        } catch (\Exception) {
            // bot offline
        }

        return view('livewire.dashboard', compact(
            'openOrders',
            'monthlyOrders',
            'monthlyRevenue',
            'totalClients',
            'recentOrders',
            'pendingDeliveries',
            'botStatus',
        ));
    }
}
