<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Clientes</h1>
        <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition" wire:navigate>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Novo Cliente
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar clientes..."
                   class="w-full sm:w-80 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2 text-sm">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 font-medium">Nome</th>
                        <th class="px-6 py-3 font-medium">Telefone</th>
                        <th class="px-6 py-3 font-medium">E-mail</th>
                        <th class="px-6 py-3 font-medium">Pedidos</th>
                        <th class="px-6 py-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($clients as $client)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $client->full_name }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $client->phone ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $client->email ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $client->orders_count }}</td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <a href="{{ route('clients.edit', $client->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm" wire:navigate>Editar</a>
                                <button wire:click="delete({{ $client->id }})" wire:confirm="Tem certeza que deseja remover este cliente?" class="text-red-600 dark:text-red-400 hover:underline text-sm">Remover</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">Nenhum cliente encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($clients->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>
