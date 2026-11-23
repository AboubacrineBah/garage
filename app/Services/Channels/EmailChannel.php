<?php

namespace App\Services\Channels;

use App\Contracts\NotificationChannelInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Canal Email (fallback classique).
 */
class EmailChannel implements NotificationChannelInterface
{
    public function getName(): string
    {
        return 'email';
    }

    public function isAvailable(): bool
    {
        return ! empty(config('mail.default')) && config('mail.default') !== 'array';
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array{success: bool, message_id?: string, error?: string, raw?: mixed}
     */
    public function send(string $to, string $message, array $options = []): array
    {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'error' => 'Mailer non configuré',
            ];
        }

        $subject = is_string($options['subject'] ?? null)
            ? $options['subject']
            : 'Notification – Garage Moderne';

        try {
            Mail::raw($message, function ($mail) use ($to, $subject) {
                $mail->to($to)->subject($subject);
            });

            return [
                'success' => true,
                'message_id' => '',
            ];
        } catch (\Throwable $e) {
            Log::error('EmailChannel exception: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
