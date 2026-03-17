<div>
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Dashboard</h1>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pedidos em Aberto</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $openOrders }}</p>
                </div>
                <div class="p-3 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Vendas este Mês</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $monthlyOrders }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Faturamento do Mês</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">R$ {{ number_format($monthlyRevenue, 2, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total de Clientes</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalClients }}</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Orders --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Pedidos Recentes</h2>
                <a href="{{ route('orders.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-6 py-3 font-medium">#</th>
                            <th class="px-6 py-3 font-medium">Cliente</th>
                            <th class="px-6 py-3 font-medium">Total</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium">Pagamento</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($recentOrders as $order)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $order->id }}</td>
                                <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $order->client->full_name }}</td>
                                <td class="px-6 py-3 text-gray-800 dark:text-gray-200">R$ {{ number_format($order->total_price, 2, ',', '.') }}</td>
                                <td class="px-6 py-3">
                                    @php
                                        $statusColors = ['pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', 'paid' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'delivered' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'];
                                        $statusLabels = ['pending' => 'Pendente', 'paid' => 'Pago', 'delivered' => 'Entregue', 'cancelled' => 'Cancelado'];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? '' }}">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    @php
                                        $payColors = ['pending' => 'text-amber-600 dark:text-amber-400', 'paid' => 'text-green-600 dark:text-green-400', 'partial' => 'text-blue-600 dark:text-blue-400'];
                                        $payLabels = ['pending' => 'Pendente', 'paid' => 'Pago', 'partial' => 'Parcial'];
                                    @endphp
                                    <span class="text-xs font-medium {{ $payColors[$order->payment_status] ?? '' }}">
                                        {{ $payLabels[$order->payment_status] ?? $order->payment_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">Nenhum pedido ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bot Status Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Status do Bot</h2>
            </div>
            <div class="p-6 text-center">
                @if ($botStatus === 'connected')
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 mb-3">
                        <span class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></span>
                        Conectado
                    </div>
                @else
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 mb-3">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        {{ $botStatus === 'qr-code' ? 'Aguardando QR Code' : 'Desconectado' }}
                    </div>
                @endif
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Entregas pendentes: {{ $pendingDeliveries }}</p>
                <a href="{{ route('bot-status') }}" class="inline-block mt-4 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Ver detalhes</a>
            </div>
        </div>
    </div>
</div>
