<div>
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('orders.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400" wire:navigate>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $orderId ? 'Editar Pedido #'.$orderId : 'Novo Pedido' }}</h1>
    </div>

    <form wire:submit="save" class="space-y-6 max-w-4xl">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Informações do Pedido</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cliente *</label>
                    <select wire:model="client_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5">
                        <option value="">Selecione...</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select wire:model="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm px-4 py-2.5">
                        <option value="pending">Pendente</option>
                        <option value="paid">Pago</option>
                        <option value="delivered">Entregue</option>
                        <option value="cancelled">Cancelado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pagamento</label>
                    <select wire:model="payment_status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm px-4 py-2.5">
                        <option value="pending">Pendente</option>
                        <option value="paid">Pago</option>
                        <option value="partial">Parcial</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data de Entrega</label>
                    <input wire:model="delivery_date" type="date" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm px-4 py-2.5">
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Itens</h2>
                <button type="button" wire:click="addItem" class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Adicionar Item
                </button>
            </div>

            <div class="space-y-4">
                @foreach ($items as $index => $item)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4" wire:key="item-{{ $index }}">
                        <div class="flex items-start justify-between mb-3">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Item {{ $index + 1 }}</span>
                            @if (count($items) > 1)
                                <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700 text-sm">Remover</button>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Produto *</label>
                                <input wire:model="items.{{ $index }}.product_name" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2">
                                @error("items.{$index}.product_name") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Descrição</label>
                                <input wire:model="items.{{ $index }}.description" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Qtd *</label>
                                <input wire:model="items.{{ $index }}.quantity" type="number" min="1" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Preço Unit. (R$) *</label>
                                <input wire:model="items.{{ $index }}.unit_price" type="number" step="0.01" min="0" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('orders.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition" wire:navigate>Cancelar</a>
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                <span wire:loading.remove wire:target="save">{{ $orderId ? 'Atualizar' : 'Criar Pedido' }}</span>
                <span wire:loading wire:target="save">Salvando...</span>
            </button>
        </div>
    </form>
</div>
