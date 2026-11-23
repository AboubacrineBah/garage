<?php

namespace App\Providers;

use App\Services\Channels\EmailChannel;
use App\Services\Channels\SmsAfricasTalkingChannel;
use App\Services\Channels\SmsOrangeChannel;
use App\Services\Channels\WhatsAppTwilioChannel;
use App\Services\Channels\WhatsAppUltraMsgChannel;
use App\Services\NotificationService;
use Illuminate\Support\ServiceProvider;

/**
 * Enregistre les canaux de notification disponibles.
 * À ajouter dans config/app.php → providers (Laravel < 11)
 * ou bootstrap/providers.php (Laravel 11+).
 */
class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NotificationService::class, function ($app) {
            $service = new NotificationService;

            // WhatsApp – priorité UltraMsg puis Twilio
            $ultraMsg = new WhatsAppUltraMsgChannel;
            if ($ultraMsg->isAvailable()) {
                $service->registerChannel($ultraMsg);
            } else {
                $twilio = new WhatsAppTwilioChannel;
                if ($twilio->isAvailable()) {
                    $service->registerChannel($twilio);
                }
            }

            // SMS – priorité Orange puis Africa's Talking
            $orange = new SmsOrangeChannel;
            if ($orange->isAvailable()) {
                $service->registerChannel($orange);
            } else {
                $at = new SmsAfricasTalkingChannel;
                if ($at->isAvailable()) {
                    $service->registerChannel($at);
                }
            }

            // Email toujours en fallback
            $service->registerChannel(new EmailChannel);

            return $service;
        });
    }

    public function boot(): void
    {
        //
    }
}
