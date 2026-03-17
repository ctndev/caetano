<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\AllowedNumbers\Index as AllowedNumbersIndex;
use App\Livewire\BotStatus;
use App\Livewire\Clients\Form as ClientForm;
use App\Livewire\Clients\Index as ClientsIndex;
use App\Livewire\Dashboard;
use App\Livewire\Financial\Index as FinancialIndex;
use App\Livewire\Messages\Index as MessagesIndex;
use App\Livewire\Orders\Form as OrderForm;
use App\Livewire\Orders\Index as OrdersIndex;
use App\Livewire\Orders\Show as OrderShow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/forgot-password', ForgotPassword::class)->name('forgot-password');
});

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/clients', ClientsIndex::class)->name('clients.index');
    Route::get('/clients/create', ClientForm::class)->name('clients.create');
    Route::get('/clients/{clientId}/edit', ClientForm::class)->name('clients.edit');

    Route::get('/orders', OrdersIndex::class)->name('orders.index');
    Route::get('/orders/create', OrderForm::class)->name('orders.create');
    Route::get('/orders/{orderId}', OrderShow::class)->name('orders.show');
    Route::get('/orders/{orderId}/edit', OrderForm::class)->name('orders.edit');

    Route::get('/financial', FinancialIndex::class)->name('financial.index');
    Route::get('/messages', MessagesIndex::class)->name('messages.index');
    Route::get('/allowed-numbers', AllowedNumbersIndex::class)->name('allowed-numbers.index');
    Route::get('/bot-status', BotStatus::class)->name('bot-status');
});
