<?php

namespace App\Services\Channels;

use App\Contracts\NotificationChannelInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Canal SMS via Orange SMS API (très courant en Afrique de l'Ouest).
 * Documentation : https://developer.orange.com
 *
 * Variables .env requises :
 * ORANGE_SMS_CLIENT_ID=...
 * ORANGE_SMS_CLIENT_SECRET=...
 * ORANGE_SMS_SENDER=GarageMod   (nom d'expéditeur autorisé)
 */
class SmsOrangeChannel implements NotificationChannelInterface
{
    protected string $clientId;

    protected string $clientSecret;

    protected string $sender;

    protected string $tokenUrl = 'https://api.orange.com/oauth/v3/token';

    protected string $smsUrl = 'https://api.orange.com/smsmessaging/v1/outbound';

    public function __construct()
    {
        $this->clientId = (string) (config('services.orange_sms.client_id') ?? '');
        $this->clientSecret = (string) (config('services.orange_sms.client_secret') ?? '');
        $this->sender = (string) (config('services.orange_sms.sender') ?? 'GarageMod');
    }

    public function getName(): string
    {
        return 'sms';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->clientId) && ! empty($this->clientSecret);
    }

    public function send(string $to, string $message, array $options = []): array
    {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'error' => 'Orange SMS non configuré',
            ];
        }

        $to = $this->normalizePhone($to);
        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            return [
                'success' => false,
                'error' => 'Impossible d\'obtenir le token OAuth Orange',
            ];
        }

        try {
            $senderAddress = 'tel:+'.preg_replace('/[^0-9]/', '', $this->sender);
            // Certaines configurations Orange utilisent un shortcode ou un nom d'expéditeur
            // Adaptez selon votre contrat.

            $payload = [
                'outboundSMSMessageRequest' => [
                    'address' => 'tel:+'.$to,
                    'senderAddress' => 'tel:+'.$this->sender,
                    'outboundSMSTextMessage' => [
                        'message' => $message,
                    ],
                ],
            ];

            // Variante plus simple souvent utilisée
            $response = Http::withToken($accessToken)
                ->timeout(15)
                ->post("{$this->smsUrl}/tel%3A%2B{$this->sender}/requests", [
                    'outboundSMSMessageRequest' => [
                        'address' => "tel:+{$to}",
                        'senderAddress' => "tel:+{$this->sender}",
                        'outboundSMSTextMessage' => [
                            'message' => mb_substr($message, 0, 160), // SMS standard
                        ],
                    ],
                ]);

            $data = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $data['outboundSMSMessageRequest']['resourceReference']['resourceURL'] ?? null,
                    'raw' => $data,
                ];
            }

            Log::warning('Orange SMS échec', ['status' => $response->status(), 'body' => $data]);

            return [
                'success' => false,
                'error' => $data['requestError']['serviceException']['text'] ?? 'Erreur Orange SMS',
                'raw' => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('Orange SMS exception: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Récupère (et met en cache) le token OAuth2 Orange.
     */
    protected function getAccessToken(): ?string
    {
        return Cache::remember('orange_sms_access_token', 3500, function () {
            try {
                $response = Http::asForm()
                    ->withBasicAuth($this->clientId, $this->clientSecret)
                    ->post($this->tokenUrl, [
                        'grant_type' => 'client_credentials',
                    ]);

                if ($response->successful()) {
                    return $response->json('access_token');
                }

                Log::error('Orange OAuth échec', ['body' => $response->body()]);

                return null;
            } catch (\Throwable $e) {
                Log::error('Orange OAuth exception: '.$e->getMessage());

                return null;
            }
        });
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 8) {
            return '223'.$phone; // Mali par défaut
        }

        return $phone;
    }
}
