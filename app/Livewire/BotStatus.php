<?php

namespace App\Livewire;

use App\Models\BotSetting;
use App\Models\Message;
use App\Services\WppConnectService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BotStatus extends Component
{
    public string $status = 'offline';
    public ?string $qrCode = null;
    public ?string $phoneNumber = null;
    public int $totalMessages = 0;
    public ?string $lastMessageAt = null;
    public int $totalPromptTokens = 0;
    public int $totalCompletionTokens = 0;
    public int $todayPromptTokens = 0;
    public int $todayCompletionTokens = 0;

    public string $aiModel = 'gpt-4.1-mini';
    public int $maxTokens = 512;
    public int $contextTimeoutMinutes = 30;
    public int $maxContextMessages = 20;

    public float $estimatedCostTotal = 0;
    public float $estimatedCostToday = 0;

    private const MODEL_PRICING = [
        'gpt-4.1'      => ['input' => 2.00, 'output' => 8.00],
        'gpt-4.1-mini' => ['input' => 0.40, 'output' => 1.60],
        'gpt-4.1-nano' => ['input' => 0.10, 'output' => 0.40],
    ];

    public function mount(): void
    {
        $this->aiModel = BotSetting::get('ai_model', 'gpt-4.1-mini');
        $this->maxTokens = (int) BotSetting::get('max_tokens', 512);
        $this->contextTimeoutMinutes = (int) BotSetting::get('context_timeout_minutes', 30);
        $this->maxContextMessages = (int) BotSetting::get('max_context_messages', 20);
        $this->refreshStatus();
    }

    public function refreshStatus(): void
    {
        $service = app(WppConnectService::class);
        $statusData = $service->getStatus();

        $this->status = $statusData['status'] ?? 'offline';
        $this->phoneNumber = $statusData['phone'] ?? null;

        if ($this->status === 'qr-code') {
            $qrData = $service->getQrCode();
            $this->qrCode = $qrData['qrcode'] ?? null;
        } else {
            $this->qrCode = null;
        }

        $this->totalMessages = Message::count();
        $lastMsg = Message::latest()->first();
        $this->lastMessageAt = $lastMsg?->created_at->format('d/m/Y H:i:s');

        $this->totalPromptTokens = (int) Message::sum('prompt_tokens');
        $this->totalCompletionTokens = (int) Message::sum('completion_tokens');

        $this->todayPromptTokens = (int) Message::whereDate('created_at', today())->sum('prompt_tokens');
        $this->todayCompletionTokens = (int) Message::whereDate('created_at', today())->sum('completion_tokens');

        $pricing = self::MODEL_PRICING[$this->aiModel] ?? self::MODEL_PRICING['gpt-4.1-mini'];
        $this->estimatedCostTotal = $this->calculateCost($this->totalPromptTokens, $this->totalCompletionTokens, $pricing);
        $this->estimatedCostToday = $this->calculateCost($this->todayPromptTokens, $this->todayCompletionTokens, $pricing);
    }

    private function calculateCost(int $promptTokens, int $completionTokens, array $pricing): float
    {
        return round(
            ($promptTokens / 1_000_000) * $pricing['input'] +
            ($completionTokens / 1_000_000) * $pricing['output'],
            4
        );
    }

    public function saveSettings(): void
    {
        $allowedModels = array_keys(self::MODEL_PRICING);
        if (! in_array($this->aiModel, $allowedModels)) {
            $this->aiModel = 'gpt-4.1-mini';
        }

        $this->maxTokens = max(64, min(4096, $this->maxTokens));
        $this->contextTimeoutMinutes = max(0, min(1440, $this->contextTimeoutMinutes));
        $this->maxContextMessages = max(2, min(50, $this->maxContextMessages));

        BotSetting::set('ai_model', $this->aiModel);
        BotSetting::set('max_tokens', $this->maxTokens);
        BotSetting::set('context_timeout_minutes', $this->contextTimeoutMinutes);
        BotSetting::set('max_context_messages', $this->maxContextMessages);

        $this->refreshStatus();

        session()->flash('success', 'Configurações salvas.');
    }

    public function render()
    {
        return view('livewire.bot-status');
    }
}
