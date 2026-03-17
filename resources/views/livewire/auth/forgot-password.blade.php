<div>
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Recuperar Senha</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Informe seu e-mail e enviaremos um link para redefinir sua senha.</p>

        @if ($sent)
            <div class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300 text-sm">
                Link de recuperação enviado para {{ $email }}.
            </div>
        @else
            <form wire:submit="sendResetLink" class="space-y-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
                    <input wire:model="email" type="email" id="email" autocomplete="email"
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5">
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <span wire:loading.remove wire:target="sendResetLink">Enviar Link</span>
                    <span wire:loading wire:target="sendResetLink">Enviando...</span>
                </button>
            </form>
        @endif

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-medium" wire:navigate>Voltar ao login</a>
        </p>
    </div>
</div>
