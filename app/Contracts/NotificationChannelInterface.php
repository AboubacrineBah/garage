<?php

namespace App\Contracts;

/**
 * Contrat pour les canaux de notification (WhatsApp, SMS, Email...).
 * Pattern Strategy / Adapter.
 */
interface NotificationChannelInterface
{
    /**
     * Envoie un message au destinataire.
     *
     * @param  string  $to  Numéro (format international) ou email
     * @param  string  $message  Contenu du message
     * @param  array  $options  Options spécifiques au provider
     * @return array{success: bool, message_id?: string, error?: string, raw?: mixed}
     */
    public function send(string $to, string $message, array $options = []): array;

    /**
     * Nom du canal (whatsapp, sms, email...).
     */
    public function getName(): string;

    /**
     * Indique si le canal est configuré et disponible.
     */
    public function isAvailable(): bool;
}
