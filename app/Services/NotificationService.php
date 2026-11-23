<?php

namespace App\Services;

use App\Contracts\NotificationChannelInterface;
use App\Models\Client;
use App\Models\NotificationLog;
use App\Models\OrdreReparation;
use App\Models\Parametre;
use Illuminate\Support\Facades\Log;

/**
 * Service d'envoi de notifications multi-canaux.
 */
class NotificationService
{
    /** @var array<string, NotificationChannelInterface> */
    protected array $channels = [];

    public function registerChannel(NotificationChannelInterface $channel): void
    {
        $this->channels[$channel->getName()] = $channel;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function send(
        string $channelName,
        string $to,
        string $message,
        string $typeNotification,
        ?OrdreReparation $ordre = null,
        ?Client $client = null,
        array $options = []
    ): NotificationLog {
        $log = NotificationLog::create([
            'ordre_id' => $ordre !== null ? $ordre->id : null,
            'client_id' => $client !== null ? $client->id : ($ordre !== null ? $ordre->client_id : null),
            'canal' => $channelName,
            'destinataire' => $to,
            'type_notification' => $typeNotification,
            'message' => $message,
            'statut' => 'en_attente',
        ]);

        $channel = $this->channels[$channelName] ?? null;

        if (! $channel || ! $channel->isAvailable()) {
            $log->update([
                'statut' => 'echec',
                'provider_response' => ['error' => "Canal {$channelName} non disponible"],
            ]);
            Log::warning("NotificationService: canal {$channelName} indisponible");

            return $log->fresh() ?? $log;
        }

        try {
            $result = $channel->send($to, $message, $options);
            $success = $result['success'];

            $log->update([
                'statut' => $success ? 'envoye' : 'echec',
                'provider_response' => $result,
                'sent_at' => $success ? now() : null,
            ]);
        } catch (\Throwable $e) {
            $log->update([
                'statut' => 'echec',
                'provider_response' => ['error' => $e->getMessage()],
            ]);
            Log::error('NotificationService: '.$e->getMessage());
        }

        return $log->fresh() ?? $log;
    }

    /**
     * @param  array<string, string>  $vars
     */
    protected function buildMessage(string $templateKey, array $vars, string $fallback): string
    {
        $template = Parametre::get($templateKey, $fallback);

        if (! is_string($template)) {
            $template = $fallback;
        }

        $replacements = [];
        foreach ($vars as $key => $value) {
            $replacements['{{'.$key.'}}'] = $value;
        }

        return strtr($template, $replacements);
    }

    /**
     * @return array<string, string>
     */
    protected function ordreVars(OrdreReparation $ordre): array
    {
        $client = $ordre->client;
        $vehicule = $ordre->vehicule;

        return [
            'client' => $client !== null ? $client->nom_complet : '',
            'immatriculation' => $vehicule !== null ? $vehicule->immatriculation : '',
            'montant' => number_format((float) $ordre->montant_ttc, 0, ',', ' ').' F CFA',
            'atelier' => (string) Parametre::get('atelier.nom', 'Garage Moderne'),
            'numero' => $ordre->numero ?? '',
        ];
    }

    public function notifierVehiculeTermine(OrdreReparation $ordre, string $canal = 'whatsapp'): ?NotificationLog
    {
        $client = $ordre->client;

        if (! $client instanceof Client || empty(trim((string) $client->telephone))) {
            return null;
        }

        $fallback = 'Bonjour {{client}}, votre véhicule {{immatriculation}} est prêt. Vous pouvez venir le récupérer. Merci de votre confiance. – {{atelier}}';

        $message = $this->buildMessage(
            'notifications.template.vehicule_termine',
            $this->ordreVars($ordre),
            $fallback
        );

        return $this->send(
            $canal,
            $client->telephone,
            $message,
            'vehicule_termine',
            $ordre,
            $client
        );
    }

    public function notifierDevisPret(OrdreReparation $ordre, string $canal = 'whatsapp'): ?NotificationLog
    {
        $client = $ordre->client;

        if (! $client instanceof Client || empty(trim((string) $client->telephone))) {
            return null;
        }

        $fallback = 'Bonjour {{client}}, le devis pour votre véhicule {{immatriculation}} est disponible. Montant : {{montant}}. Contactez-nous pour validation. – {{atelier}}';

        $message = $this->buildMessage(
            'notifications.template.devis_pret',
            $this->ordreVars($ordre),
            $fallback
        );

        return $this->send(
            $canal,
            $client->telephone,
            $message,
            'devis_pret',
            $ordre,
            $client
        );
    }
}
