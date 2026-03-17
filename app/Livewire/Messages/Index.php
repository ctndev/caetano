<?php

namespace App\Livewire\Messages;

use App\Models\Message;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $directionFilter = '';
    public string $typeFilter = '';

    public function updatedDirectionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $messages = Message::with('allowedNumber')
            ->when($this->directionFilter, fn ($q) => $q->where('direction', $this->directionFilter))
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->latest()
            ->paginate(20);

        return view('livewire.messages.index', compact('messages'));
    }
}
