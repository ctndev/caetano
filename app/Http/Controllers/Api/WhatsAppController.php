<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\SalesAssistant;
use App\Http\Controllers\Controller;
use App\Models\AllowedNumber;
use App\Models\BotSetting;
use App\Models\Message;
use App\Services\WppConnectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Transcription;

class WhatsAppController extends Controller
{
    public function receiveMessage(Request $request): JsonResponse
    {
        $request->validate([
            'number' => 'required|string',
            'message' => 'nullable|string',
            'type' => 'nullable|string|in:text,audio',
            'audio' => 'nullable|file',
        ]);

        $phoneNumber = preg_replace('/\D/', '', $request->input('number'));
        $type = $request->input('type', 'text');

        $allowedNumber = AllowedNumber::active()
            ->where('phone_number', $phoneNumber)
            ->first();

        if (! $allowedNumber) {
            return response()->json(['error' => 'Número não autorizado'], 403);
        }

        $messageContent = $request->input('message', '');

        if ($type === 'audio' && $request->hasFile('audio')) {
            try {
                $transcript = Transcription::fromUpload($request->file('audio'))->generate();
                $messageContent = (string) $transcript;
                $type = 'audio';
            } catch (\Exception $e) {
                Log::error('Audio transcription failed', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Audio transcription failed'], 500);
            }
        }

        Message::create([
            'allowed_number_id' => $allowedNumber->id,
            'direction' => 'in',
            'content' => $messageContent,
            'type' => $type,
        ]);

        try {
            $agent = new SalesAssistant();

            if ($this->shouldResetContext($allowedNumber)) {
                $agent->forUser($allowedNumber);
            } else {
                $agent->continueLastConversation($allowedNumber);
            }

            $response = $agent->prompt($messageContent);
            $replyText = (string) $response;
        } catch (\Exception $e) {
            Log::error('AI Agent failed', [
                'error' => $e->getMessage(),
                'number' => $phoneNumber,
                'message' => $messageContent,
            ]);

            return response()->json(['error' => 'AI processing failed'], 500);
        }

        $usage = $response->usage;

        Message::create([
            'allowed_number_id' => $allowedNumber->id,
            'direction' => 'out',
            'content' => $replyText,
            'type' => 'text',
            'ai_response' => ['original_message' => $messageContent, 'type' => $type],
            'prompt_tokens' => $usage->promptTokens ?? 0,
            'completion_tokens' => $usage->completionTokens ?? 0,
        ]);

        return response()->json(['reply' => $replyText]);
    }

    private function shouldResetContext(AllowedNumber $allowedNumber): bool
    {
        $timeoutMinutes = (int) BotSetting::get('context_timeout_minutes', 30);

        if ($timeoutMinutes <= 0) {
            return false;
        }

        $lastMessage = Message::where('allowed_number_id', $allowedNumber->id)
            ->where('direction', 'out')
            ->latest()
            ->first();

        if (! $lastMessage) {
            return true;
        }

        return $lastMessage->created_at->diffInMinutes(now()) >= $timeoutMinutes;
    }

    public function status(WppConnectService $service): JsonResponse
    {
        return response()->json($service->getStatus());
    }

    public function qrCode(WppConnectService $service): JsonResponse
    {
        return response()->json($service->getQrCode());
    }
}
