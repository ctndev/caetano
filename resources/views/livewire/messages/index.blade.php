<div>
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Mensagens</h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-wrap gap-3">
            <select wire:model.live="directionFilter" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2">
                <option value="">Todas Direções</option>
                <option value="in">Recebidas</option>
                <option value="out">Enviadas</option>
            </select>
            <select wire:model.live="typeFilter" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2">
                <option value="">Todos Tipos</option>
                <option value="text">Texto</option>
                <option value="audio">Áudio</option>
            </select>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse ($messages as $message)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium {{ $message->direction === 'in' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                                    {{ $message->direction === 'in' ? 'Recebida' : 'Enviada' }}
                                </span>
                                @if ($message->type === 'audio')
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">Áudio</span>
                                @endif
                                @if ($message->allowedNumber)
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $message->allowedNumber->name }} ({{ $message->allowedNumber->phone_number }})</span>
                                @endif
                                @if ($message->direction === 'out' && ($message->prompt_tokens > 0 || $message->completion_tokens > 0))
                                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400" title="Prompt: {{ $message->prompt_tokens }} | Resposta: {{ $message->completion_tokens }}">
                                        {{ number_format($message->prompt_tokens + $message->completion_tokens) }} tokens
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ \Illuminate\Support\Str::limit($message->content, 300) }}</p>
                        </div>
                        <span class="text-xs text-gray-400 whitespace-nowrap">{{ $message->created_at->format('d/m H:i') }}</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-400">Nenhuma mensagem ainda.</div>
            @endforelse
        </div>

        @if ($messages->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
