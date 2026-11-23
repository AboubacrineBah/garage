<?php

namespace App\Services\Channels;

use App\Contracts\NotificationChannelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Canal WhatsApp via Twilio (WhatsApp Business API).
 *
 * Variables .env requises :
 * TWILIO_SID=ACxxxxxxxx
 * TWILIO_TOKEN=votre_token
 * TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
 */
class WhatsAppTwilioChannel implements NotificationChannelInterface
{
    protected string $sid;

    protected string $token;

    protected string $from;

    public function __construct()
    {
        $this->sid = (string) (config('services.twilio.sid') ?? '');
        $this->token = (string) (config('services.twilio.token') ?? '');
        $this->from = (string) (config('services.twilio.whatsapp_from') ?? '');
    }

    public function getName(): string
    {
        return 'whatsapp';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->sid) && ! empty($this->token) && ! empty($this->from);
    }

    public function send(string $to, string $message, array $options = []): array
    {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'error' => 'Twilio WhatsApp non configuré',
            ];
        }

        $to = $this->normalizePhone($to);

        try {
            $response = Http::withBasicAuth($this->sid, $this->token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->sid}/Messages.json", [
                    'From' => $this->from,
                    'To' => 'whatsapp:'.$to,
                    'Body' => $message,
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['sid'])) {
                return [
                    'success' => true,
                    'message_id' => $data['sid'],
                    'raw' => $data,
                ];
            }

            Log::warning('Twilio WhatsApp échec', ['response' => $data]);

            return [
                'success' => false,
                'error' => $data['message'] ?? 'Erreur Twilio',
                'raw' => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('Twilio WhatsApp exception: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (! str_starts_with($phone, '+')) {
            // Mali par défaut
            if (strlen(preg_replace('/[^0-9]/', '', $phone)) === 8) {
                $phone = '+223'.preg_replace('/[^0-9]/', '', $phone);
            } else {
                $phone = '+'.preg_replace('/[^0-9]/', '', $phone);
            }
        }

        return $phone;
    }
}
