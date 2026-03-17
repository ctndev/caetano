<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?int $clientId = null;

    #[Validate('required|string|max:255')]
    public string $first_name = '';

    #[Validate('nullable|string|max:255')]
    public string $last_name = '';

    #[Validate('nullable|string|max:20')]
    public string $phone = '';

    #[Validate('nullable|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string')]
    public string $notes = '';

    public function mount(?int $clientId = null): void
    {
        if ($clientId) {
            $this->clientId = $clientId;
            $client = Client::findOrFail($clientId);
            $this->first_name = $client->first_name;
            $this->last_name = $client->last_name ?? '';
            $this->phone = $client->phone ?? '';
            $this->email = $client->email ?? '';
            $this->notes = $client->notes ?? '';
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name ?: null,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'notes' => $this->notes ?: null,
        ];

        if ($this->clientId) {
            Client::findOrFail($this->clientId)->update($data);
            session()->flash('success', 'Cliente atualizado com sucesso.');
        } else {
            Client::create($data);
            session()->flash('success', 'Cliente criado com sucesso.');
        }

        $this->redirect(route('clients.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.clients.form');
    }
}
