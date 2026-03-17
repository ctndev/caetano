<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Pedidos</h1>
        <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition" wire:navigate>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Novo Pedido
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap gap-3">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por cliente..."
                   class="w-full sm:w-64 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 text-sm">
            <select wire:model.live="statusFilter" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2">
                <option value="">Status do Pedido</option>
                <option value="pending">Em Produção</option>
                <option value="ready">Pronto</option>
                <option value="delivered">Entregue</option>
                <option value="cancelled">Cancelado</option>
            </select>
            <select wire:model.live="paymentFilter" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2">
                <option value="">Todos Pagamentos</option>
                <option value="pending">Pendente</option>
                <option value="paid">Pago</option>
                <option value="partial">Parcial</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 font-medium">#</th>
                        <th class="px-6 py-3 font-medium">Cliente</th>
                        <th class="px-6 py-3 font-medium">Itens</th>
                        <th class="px-6 py-3 font-medium">Total</th>
                        <th class="px-6 py-3 font-medium">Entrega</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Pagamento</th>
                        <th class="px-6 py-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $order->id }}</td>
                            <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $order->client->full_name }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $order->items_count }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">R$ {{ number_format($order->total_price, 2, ',', '.') }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $order->delivery_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $sc = ['pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', 'ready' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'delivered' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400', 'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'];
                                    $sl = ['pending' => 'Em Produção', 'ready' => 'Pronto', 'delivered' => 'Entregue', 'cancelled' => 'Cancelado'];
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $sc[$order->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">{{ $sl[$order->status] ?? ucfirst($order->status) }}</span>
                            </td>
                            <td class="px-6 py-3">
                                @php
                                    $pc = ['pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400', 'partial' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400', 'paid' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'];
                                    $pl = ['pending' => 'Pendente', 'paid' => 'Pago', 'partial' => 'Parcial'];
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $pc[$order->payment_status] ?? '' }}">{{ $pl[$order->payment_status] ?? $order->payment_status }}</span>
                            </td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <a href="{{ route('orders.show', $order->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm" wire:navigate>Ver</a>
                                <a href="{{ route('orders.edit', $order->id) }}" class="text-gray-600 dark:text-gray-400 hover:underline text-sm" wire:navigate>Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">Nenhum pedido encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
