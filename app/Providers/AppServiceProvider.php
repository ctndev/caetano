<?php

namespace App\Providers;

use App\Listeners\LogAiDebug;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Ai\Events\AgentPrompted;
use Laravel\Ai\Events\PromptingAgent;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $listener = new LogAiDebug;

        Event::listen(PromptingAgent::class, [$listener, 'handlePrompting']);
        Event::listen(AgentPrompted::class, [$listener, 'handlePrompted']);
    }
}
