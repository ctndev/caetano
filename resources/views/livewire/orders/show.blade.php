<div>
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400" wire:navigate>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Pedido #{{ $order->id }}</h1>
        <a href="{{ route('orders.edit', $order->id) }}" class="ml-auto text-sm text-indigo-600 dark:text-indigo-400 hover:underline" wire:navigate>Editar</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Order Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Itens do Pedido</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="pb-3 font-medium">Produto</th>
                                <th class="pb-3 font-medium">Descrição</th>
                                <th class="pb-3 font-medium text-center">Qtd</th>
                                <th class="pb-3 font-medium text-right">Preço Unit.</th>
                                <th class="pb-3 font-medium text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="py-3 text-gray-800 dark:text-gray-200 font-medium">{{ $item->product_name }}</td>
                                    <td class="py-3 text-gray-600 dark:text-gray-400">{{ $item->description ?? '-' }}</td>
                                    <td class="py-3 text-gray-800 dark:text-gray-200 text-center">{{ $item->quantity }}</td>
                                    <td class="py-3 text-gray-800 dark:text-gray-200 text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                    <td class="py-3 text-gray-800 dark:text-gray-200 text-right font-medium">R$ {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-200 dark:border-gray-600">
                                <td colspan="4" class="pt-3 text-right font-semibold text-gray-800 dark:text-white">Total:</td>
                                <td class="pt-3 text-right font-bold text-lg text-gray-800 dark:text-white">R$ {{ number_format($order->total_price, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Detalhes</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Cliente</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $order->client->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Data de Entrega</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $order->delivery_date?->format('d/m/Y') ?? 'Não definida' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 dark:text-gray-400">Criado em</dt>
                        <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $order->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Status</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Status do Pedido</label>
                        <select wire:change="updateStatus($event.target.value)" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2">
                            @foreach (['pending' => 'Pendente', 'paid' => 'Pago', 'delivered' => 'Entregue', 'cancelled' => 'Cancelado'] as $val => $label)
                                <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-500 dark:text-gray-400 mb-1">Status do Pagamento</label>
                        <select wire:change="updatePaymentStatus($event.target.value)" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2">
                            @foreach (['pending' => 'Pendente', 'paid' => 'Pago', 'partial' => 'Parcial'] as $val => $label)
                                <option value="{{ $val }}" {{ $order->payment_status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
