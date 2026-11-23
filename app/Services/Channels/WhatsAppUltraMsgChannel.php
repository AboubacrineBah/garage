<?php

namespace App\Services\Channels;

use App\Contracts\NotificationChannelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Canal WhatsApp via UltraMsg (simple et adapté aux PME ouest-africaines).
 * Documentation : https://ultramsg.com
 *
 * Variables .env requises :
 * WHATSAPP_PROVIDER=ultramsg
 * ULTRAMSG_INSTANCE_ID=instanceXXXX
 * ULTRAMSG_TOKEN=votre_token
 */
class WhatsAppUltraMsgChannel implements NotificationChannelInterface
{
    protected string $instanceId;

    protected string $token;

    protected string $baseUrl = 'https://api.ultramsg.com';

    public function __construct()
    {
        $this->instanceId = (string) (config('services.ultramsg.instance_id') ?? '');
        $this->token = (string) (config('services.ultramsg.token') ?? '');
    }

    public function getName(): string
    {
        return 'whatsapp';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->instanceId) && ! empty($this->token);
    }

    /**
     * Envoie un message texte WhatsApp.
     *
     * @param  string  $to  Numéro au format international (ex: 22370000000 ou +22370000000)
     * @param  string  $message  Contenu du message
     * @param  array  $options  Options supplémentaires (priority, referenceId...)
     */
    public function send(string $to, string $message, array $options = []): array
    {
        if (! $this->isAvailable()) {
            return [
                'success' => false,
                'error' => 'UltraMsg non configuré (INSTANCE_ID ou TOKEN manquant)',
            ];
        }

        $to = $this->normalizePhone($to);

        try {
            $response = Http::timeout(15)
                ->asForm()
                ->post("{$this->baseUrl}/{$this->instanceId}/messages/chat", [
                    'token' => $this->token,
                    'to' => $to,
                    'body' => $message,
                    'priority' => $options['priority'] ?? 10,
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['sent']) && $data['sent'] === 'true') {
                return [
                    'success' => true,
                    'message_id' => $data['id'] ?? null,
                    'raw' => $data,
                ];
            }

            Log::warning('UltraMsg échec', ['response' => $data, 'to' => $to]);

            return [
                'success' => false,
                'error' => $data['error'] ?? $data['message'] ?? 'Erreur inconnue UltraMsg',
                'raw' => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('UltraMsg exception: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Normalise le numéro au format international sans le signe +.
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Si le numéro commence déjà par l'indicatif pays (ex: 223 pour le Mali)
        if (strlen($phone) >= 11) {
            return $phone;
        }

        // Par défaut on suppose un numéro malien (indicatif 223)
        if (strlen($phone) === 8) {
            return '223'.$phone;
        }

        return $phone;
    }
}
