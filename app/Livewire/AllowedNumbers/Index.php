<?php

namespace App\Livewire\AllowedNumbers;

use App\Models\AllowedNumber;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    public bool $showForm = false;
    public ?int $editingId = null;

    #[Validate('required|string|max:20')]
    public string $phone_number = '';

    #[Validate('required|string|max:255')]
    public string $name = '';

    public bool $is_active = true;

    public function openForm(?int $id = null): void
    {
        $this->resetValidation();
        if ($id) {
            $number = AllowedNumber::findOrFail($id);
            $this->editingId = $id;
            $this->phone_number = $number->phone_number;
            $this->name = $number->name;
            $this->is_active = $number->is_active;
        } else {
            $this->editingId = null;
            $this->phone_number = '';
            $this->name = '';
            $this->is_active = true;
        }
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->reset(['phone_number', 'name', 'is_active']);
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            $number = AllowedNumber::findOrFail($this->editingId);
            $number->update([
                'phone_number' => $this->phone_number,
                'name' => $this->name,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Número atualizado.');
        } else {
            AllowedNumber::create([
                'phone_number' => $this->phone_number,
                'name' => $this->name,
                'is_active' => $this->is_active,
            ]);
            session()->flash('success', 'Número adicionado.');
        }

        $this->closeForm();
    }

    public function toggleActive(int $id): void
    {
        $number = AllowedNumber::findOrFail($id);
        $number->update(['is_active' => !$number->is_active]);
    }

    public function delete(int $id): void
    {
        AllowedNumber::findOrFail($id)->delete();
        session()->flash('success', 'Número removido.');
    }

    public function render()
    {
        $numbers = AllowedNumber::withCount('messages')->latest()->get();
        return view('livewire.allowed-numbers.index', compact('numbers'));
    }
}
