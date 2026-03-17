<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Financeiro</h1>
        <select wire:model.live="period" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2">
            <option value="week">Esta Semana</option>
            <option value="month">Este Mês</option>
            <option value="year">Este Ano</option>
        </select>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Receita Recebida</div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $paidOrders }} pedidos pagos</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">A Receber</div>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">R$ {{ number_format($totalPending, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $pendingOrders }} pendentes + {{ $partialOrders }} parciais</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Total de Vendas</div>
            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ $totalSales }}</div>
            <div class="text-xs text-gray-400 mt-1">no período selecionado</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5">
            <div class="text-sm text-gray-500 dark:text-gray-400">Pagamentos Parciais</div>
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $partialOrders }}</div>
            <div class="text-xs text-gray-400 mt-1">pedidos com pagamento parcial</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Monthly Summary --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Resumo Mensal</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 font-medium">Mês</th>
                            <th class="px-4 py-3 font-medium">Vendas</th>
                            <th class="px-4 py-3 font-medium">Total</th>
                            <th class="px-4 py-3 font-medium">Recebido</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($monthlyData as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->translatedFormat('M/Y') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $row->orders_count }}</td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200">R$ {{ number_format($row->total_sales, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-green-600 dark:text-green-400">R$ {{ number_format($row->total_received, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">Sem dados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Clients --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Top Clientes</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 font-medium">Cliente</th>
                            <th class="px-4 py-3 font-medium">Pedidos</th>
                            <th class="px-4 py-3 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse ($topClients as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $row->client->full_name }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $row->orders_count }}</td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200">R$ {{ number_format($row->total_spent, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">Sem dados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Payments --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Últimos Pagamentos</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 font-medium">Data</th>
                        <th class="px-4 py-3 font-medium">Cliente</th>
                        <th class="px-4 py-3 font-medium">Pedido</th>
                        <th class="px-4 py-3 font-medium">Valor</th>
                        <th class="px-4 py-3 font-medium">Método</th>
                        <th class="px-4 py-3 font-medium">Obs</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($recentPayments as $payment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $payment->paid_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $payment->order->client->full_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">#{{ $payment->order_id }}</td>
                            <td class="px-4 py-3 text-green-600 dark:text-green-400 font-medium">R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                @php
                                    $methods = ['pix' => 'PIX', 'dinheiro' => 'Dinheiro', 'cartao' => 'Cartão', 'transferencia' => 'Transferência', 'outro' => 'Outro', 'other' => 'Outro'];
                                @endphp
                                {{ $methods[$payment->method] ?? $payment->method }}
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $payment->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Nenhum pagamento registrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
