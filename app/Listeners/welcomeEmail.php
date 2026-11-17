<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;
use Mail;

class welcomeEmail
{
    /**
     * Create the event listener.
     */
    public $user;
    public function __construct(User $data)
    {
        $this->user = $data;
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $user = $event->user;
        Log::info("sending welcome email : {$user->email} (id : {$user->id})");
    }
}
