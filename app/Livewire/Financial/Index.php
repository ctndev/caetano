<?php

namespace App\Livewire\Financial;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public string $period = 'month';

    public function render()
    {
        $startDate = match ($this->period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $totalRevenue = Order::where('created_at', '>=', $startDate)
            ->sum('amount_paid');

        $totalPending = Order::whereIn('payment_status', ['pending', 'partial'])
            ->sum(DB::raw('total_price - amount_paid'));

        $totalSales = Order::where('created_at', '>=', $startDate)->count();

        $paidOrders = Order::where('created_at', '>=', $startDate)
            ->where('payment_status', 'paid')
            ->count();

        $partialOrders = Order::where('payment_status', 'partial')->count();
        $pendingOrders = Order::where('payment_status', 'pending')
            ->whereIn('status', ['pending', 'paid'])
            ->count();

        $recentPayments = Payment::with('order.client')
            ->latest('paid_at')
            ->limit(20)
            ->get();

        $monthlyData = Order::select(
                DB::raw("strftime('%Y-%m', created_at) as month"),
                DB::raw('SUM(total_price) as total_sales'),
                DB::raw('SUM(amount_paid) as total_received'),
                DB::raw('COUNT(*) as orders_count')
            )
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $topClients = Order::with('client')
            ->select('client_id', DB::raw('SUM(total_price) as total_spent'), DB::raw('COUNT(*) as orders_count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('client_id')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        return view('livewire.financial.index', compact(
            'totalRevenue', 'totalPending', 'totalSales', 'paidOrders',
            'partialOrders', 'pendingOrders', 'recentPayments',
            'monthlyData', 'topClients'
        ));
    }
}
