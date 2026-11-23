<?php

namespace App\Services\Channels;

use App\Contracts\NotificationChannelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Canal SMS via Africa's Talking (très utilisé en Afrique).
 * Documentation : https://africastalking.com
 *
 * Variables .env requises :
 * AFRICASTALKING_USERNAME=sandbox ou votre username
 * AFRICASTALKING_API_KEY=votre_api_key
 * AFRICASTALKING_FROM=GarageMod   (Sender ID optionnel)
 */
class SmsAfricasTalkingChannel implements NotificationChannelInterface
{
    protected string $username;

    protected string $apiKey;

    protected string $from;

    protected string $baseUrl = 'https://api.africastalking.com/version1/messaging';

    public function __construct()
    {
        $this->username = (string) (config('services.africastalking.username') ?? '');
        $this->apiKey = (string) (config('services.africastalking.api_key') ?? '');
        $this->from = (string) (config('services.africastalking.from') ?? '');
    }

    public function getName(): string
    {
        return 'sms';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->username) && ! empty($this->apiKey);
    }

    public function send(string $to, string $message, array $options = []): array
    {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'error' => 'Africa\'s Talking non configuré',
            ];
        }

        $to = $this->normalizePhone($to);

        try {
            $payload = [
                'username' => $this->username,
                'to' => $to,
                'message' => $message,
            ];

            if (! empty($this->from)) {
                $payload['from'] = $this->from;
            }

            $response = Http::withHeaders([
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ])
                ->asForm()
                ->timeout(15)
                ->post($this->baseUrl, $payload);

            $data = $response->json();

            // Structure de réponse Africa's Talking
            $recipients = $data['SMSMessageData']['Recipients'] ?? [];
            $first = $recipients[0] ?? null;

            if ($response->successful() && $first && in_array($first['status'] ?? '', ['Success', 'Sent'], true)) {
                return [
                    'success' => true,
                    'message_id' => $first['messageId'] ?? null,
                    'raw' => $data,
                ];
            }

            $errorMsg = $first['status'] ?? ($data['SMSMessageData']['Message'] ?? 'Erreur Africa\'s Talking');

            Log::warning('Africa\'s Talking échec', ['response' => $data]);

            return [
                'success' => false,
                'error' => $errorMsg,
                'raw' => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('Africa\'s Talking exception: '.$e->getMessage());

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
            $digits = preg_replace('/[^0-9]/', '', $phone);
            if (strlen($digits) === 8) {
                $phone = '+223'.$digits;
            } else {
                $phone = '+'.$digits;
            }
        }

        return $phone;
    }
}
