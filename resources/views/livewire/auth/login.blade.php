<div>
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Entrar</h2>

        <form wire:submit="login" class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
                <input wire:model="email" type="email" id="email" autocomplete="email"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senha</label>
                <input wire:model="password" type="password" id="password" autocomplete="current-password"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5">
                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2">
                    <input wire:model="remember" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Lembrar de mim</span>
                </label>
                <a href="{{ route('forgot-password') }}" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400" wire:navigate>
                    Esqueceu a senha?
                </a>
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span wire:loading.remove wire:target="login">Entrar</span>
                <span wire:loading wire:target="login">Entrando...</span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Não tem conta?
            <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-medium" wire:navigate>Criar conta</a>
        </p>
    </div>
</div>
