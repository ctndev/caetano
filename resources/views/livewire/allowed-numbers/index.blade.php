<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Números Permitidos</h1>
        <button wire:click="openForm" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Adicionar
        </button>
    </div>

    @if ($showForm)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-6 max-w-lg">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">{{ $editingId ? 'Editar' : 'Novo' }} Número</h2>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número (com DDD) *</label>
                    <input wire:model="phone_number" type="text" placeholder="5585999999999" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm px-4 py-2.5 text-sm">
                    @error('phone_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome *</label>
                    <input wire:model="name" type="text" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm px-4 py-2.5 text-sm">
                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <label class="flex items-center gap-2">
                    <input wire:model="is_active" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Ativo</span>
                </label>
                <div class="flex gap-3">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">Salvar</button>
                    <button type="button" wire:click="closeForm" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 rounded-lg transition">Cancelar</button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 font-medium">Número</th>
                        <th class="px-6 py-3 font-medium">Nome</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Mensagens</th>
                        <th class="px-6 py-3 font-medium text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($numbers as $number)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-3 font-mono text-gray-800 dark:text-gray-200">{{ $number->phone_number }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-200">{{ $number->name }}</td>
                            <td class="px-6 py-3">
                                <button wire:click="toggleActive({{ $number->id }})" class="px-2 py-1 rounded-full text-xs font-medium {{ $number->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ $number->is_active ? 'Ativo' : 'Inativo' }}
                                </button>
                            </td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ $number->messages_count }}</td>
                            <td class="px-6 py-3 text-right space-x-2">
                                <button wire:click="openForm({{ $number->id }})" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">Editar</button>
                                <button wire:click="delete({{ $number->id }})" wire:confirm="Tem certeza?" class="text-red-600 dark:text-red-400 hover:underline text-sm">Remover</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">Nenhum número cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
