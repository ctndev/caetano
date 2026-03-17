<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $client = Client::findOrFail($id);
        $client->delete();
        session()->flash('success', "Cliente {$client->full_name} removido.");
    }

    public function render()
    {
        $clients = Client::query()
            ->when($this->search, fn ($q) => $q
                ->where('first_name', 'like', "%{$this->search}%")
                ->orWhere('last_name', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
            )
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        return view('livewire.clients.index', compact('clients'));
    }
}
