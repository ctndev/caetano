<div>
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Criar Conta</h2>

        <form wire:submit="register" class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome</label>
                <input wire:model="name" type="text" id="name" autocomplete="name"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
                <input wire:model="email" type="email" id="email" autocomplete="email"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Senha</label>
                <input wire:model="password" type="password" id="password" autocomplete="new-password"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5">
                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar Senha</label>
                <input wire:model="password_confirmation" type="password" id="password_confirmation" autocomplete="new-password"
                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-2.5">
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span wire:loading.remove wire:target="register">Criar Conta</span>
                <span wire:loading wire:target="register">Criando...</span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Já tem conta?
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-medium" wire:navigate>Entrar</a>
        </p>
    </div>
</div>
