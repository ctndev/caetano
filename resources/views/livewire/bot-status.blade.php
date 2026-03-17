<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Status do Bot</h1>
        <button wire:click="refreshStatus" class="inline-flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
            <svg class="w-4 h-4" wire:loading.class="animate-spin" wire:target="refreshStatus" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Atualizar
        </button>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" wire:poll.10s="refreshStatus">
        {{-- Status Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Conexão WhatsApp</h2>

            <div class="flex items-center gap-3 mb-6">
                @if ($status === 'connected')
                    <span class="flex items-center gap-2 px-4 py-2 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-sm font-medium">
                        <span class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></span>
                        Conectado
                    </span>
                @elseif ($status === 'qr-code')
                    <span class="flex items-center gap-2 px-4 py-2 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-sm font-medium">
                        <span class="w-3 h-3 rounded-full bg-amber-500 animate-pulse"></span>
                        Aguardando QR Code
                    </span>
                @else
                    <span class="flex items-center gap-2 px-4 py-2 rounded-full bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-sm font-medium">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        Desconectado
                    </span>
                @endif
            </div>

            <dl class="space-y-3 text-sm">
                @if ($phoneNumber)
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Número</dt>
                        <dd class="font-mono text-gray-800 dark:text-gray-200">{{ $phoneNumber }}</dd>
                    </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">Total de Mensagens</dt>
                    <dd class="font-medium text-gray-800 dark:text-gray-200">{{ $totalMessages }}</dd>
                </div>
                @if ($lastMessageAt)
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">Última Mensagem</dt>
                        <dd class="text-gray-800 dark:text-gray-200">{{ $lastMessageAt }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- QR Code Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">QR Code</h2>

            @if ($qrCode)
                <div class="flex flex-col items-center">
                    <img src="{{ $qrCode }}" alt="QR Code WhatsApp" class="w-64 h-64 rounded-lg border border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">Escaneie com o WhatsApp para conectar</p>
                </div>
            @elseif ($status === 'connected')
                <div class="flex flex-col items-center py-8 text-center">
                    <svg class="w-16 h-16 text-green-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-gray-600 dark:text-gray-400">Bot conectado e funcionando.</p>
                </div>
            @else
                <div class="flex flex-col items-center py-8 text-center">
                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.07 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <p class="text-gray-500 dark:text-gray-400">Servidor do bot não está acessível.</p>
                    <p class="text-xs text-gray-400 mt-1">Verifique se o server-node está rodando.</p>
                </div>
            @endif
        </div>

        {{-- Token Usage & Cost Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Uso de Tokens & Custo Estimado</h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prompt (Total)</div>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-1">{{ number_format($totalPromptTokens) }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Resposta (Total)</div>
                    <div class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-1">{{ number_format($totalCompletionTokens) }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prompt (Hoje)</div>
                    <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($todayPromptTokens) }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Resposta (Hoje)</div>
                    <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($todayCompletionTokens) }}</div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Total tokens: <strong>{{ number_format($totalPromptTokens + $totalCompletionTokens) }}</strong>
                        &middot; Custo estimado: <strong>${{ number_format($estimatedCostTotal, 4) }}</strong>
                    </p>
                </div>
                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <p class="text-sm text-green-700 dark:text-green-300">
                        Hoje: <strong>{{ number_format($todayPromptTokens + $todayCompletionTokens) }}</strong> tokens
                        &middot; Custo estimado: <strong>${{ number_format($estimatedCostToday, 4) }}</strong>
                    </p>
                </div>
            </div>

            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                * Custo calculado com base no modelo <strong>{{ $aiModel }}</strong>. Valores em USD aproximados.
            </p>
        </div>

        {{-- Settings Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 lg:col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Configurações do Bot</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- AI Model --}}
                <div>
                    <label for="aiModel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Modelo IA</label>
                    <select wire:model="aiModel" id="aiModel"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="gpt-4.1">GPT-4.1 (melhor, $2.00/M in)</option>
                        <option value="gpt-4.1-mini">GPT-4.1 Mini ($0.40/M in)</option>
                        <option value="gpt-4.1-nano">GPT-4.1 Nano ($0.10/M in)</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Nano é o mais econômico. Mini tem bom custo-benefício.</p>
                </div>

                {{-- Max Tokens --}}
                <div>
                    <label for="maxTokens" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tokens por Resposta</label>
                    <input wire:model="maxTokens" type="number" id="maxTokens" min="64" max="4096" step="64"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Tamanho máximo da resposta (64-4096).</p>
                </div>

                {{-- Context Timeout --}}
                <div>
                    <label for="contextTimeoutMinutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reset de Contexto (min)</label>
                    <input wire:model="contextTimeoutMinutes" type="number" id="contextTimeoutMinutes" min="0" max="1440" step="5"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Minutos sem interação para reiniciar contexto. 0 = nunca.</p>
                </div>

                {{-- Max Context Messages --}}
                <div>
                    <label for="maxContextMessages" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mensagens de Contexto</label>
                    <input wire:model="maxContextMessages" type="number" id="maxContextMessages" min="2" max="50" step="1"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-400 mt-1">Nº de mensagens enviadas como contexto (2-50). Menos = mais barato.</p>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-4">
                <button wire:click="saveSettings"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                    <svg class="w-4 h-4" wire:loading.class="animate-spin" wire:target="saveSettings" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Salvar Configurações
                </button>
                <span wire:loading wire:target="saveSettings" class="text-sm text-gray-500">Salvando...</span>
            </div>

            {{-- Cost Optimization Tips --}}
            <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-300 mb-2">Dicas para reduzir custos</h3>
                <ul class="text-xs text-amber-700 dark:text-amber-400 space-y-1 list-disc list-inside">
                    <li>Use <strong>GPT-4.1 Nano</strong> para tarefas simples (20x mais barato que 4.1)</li>
                    <li>Reduza <strong>Mensagens de Contexto</strong> para 5-10 (menos tokens de prompt)</li>
                    <li>Configure <strong>Reset de Contexto</strong> para 15-30 min (evita acumular histórico)</li>
                    <li>Mantenha <strong>Tokens por Resposta</strong> em 256-512 (respostas mais curtas)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
