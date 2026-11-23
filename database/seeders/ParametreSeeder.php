<?php

namespace Database\Seeders;

use App\Models\Parametre;
use Illuminate\Database\Seeder;

class ParametreSeeder extends Seeder
{
    public function run(): void
    {
        $params = [
            // Groupe atelier
            ['cle' => 'atelier.nom', 'valeur' => 'Garage Moderne', 'type' => 'string', 'groupe' => 'atelier', 'libelle' => 'Nom de l\'atelier'],
            ['cle' => 'atelier.adresse', 'valeur' => 'Bamako, Mali', 'type' => 'string', 'groupe' => 'atelier', 'libelle' => 'Adresse'],
            ['cle' => 'atelier.telephone', 'valeur' => '+223 70 00 00 00', 'type' => 'string', 'groupe' => 'atelier', 'libelle' => 'Téléphone'],
            ['cle' => 'atelier.email', 'valeur' => 'contact@garage-moderne.local', 'type' => 'string', 'groupe' => 'atelier', 'libelle' => 'Email'],
            ['cle' => 'atelier.nif', 'valeur' => '', 'type' => 'string', 'groupe' => 'atelier', 'libelle' => 'NIF'],
            ['cle' => 'atelier.rc', 'valeur' => '', 'type' => 'string', 'groupe' => 'atelier', 'libelle' => 'Registre de commerce'],

            // Groupe facturation
            ['cle' => 'facturation.devis_validite_jours', 'valeur' => '15', 'type' => 'int', 'groupe' => 'facturation', 'libelle' => 'Validité des devis (jours)'],
            ['cle' => 'facturation.devise_defaut', 'valeur' => 'XOF', 'type' => 'string', 'groupe' => 'facturation', 'libelle' => 'Devise par défaut'],

            // Groupe notifications – templates
            [
                'cle' => 'notifications.template.devis_pret',
                'valeur' => 'Bonjour {{client}}, le devis pour votre véhicule {{immatriculation}} est disponible. Montant : {{montant}}. Contactez-nous pour validation. – {{atelier}}',
                'type' => 'text',
                'groupe' => 'notifications',
                'libelle' => 'Template WhatsApp/SMS – Devis prêt',
            ],
            [
                'cle' => 'notifications.template.vehicule_termine',
                'valeur' => 'Bonjour {{client}}, votre véhicule {{immatriculation}} est prêt. Vous pouvez venir le récupérer. Merci de votre confiance. – {{atelier}}',
                'type' => 'text',
                'groupe' => 'notifications',
                'libelle' => 'Template WhatsApp/SMS – Véhicule terminé',
            ],
            [
                'cle' => 'notifications.template.rappel_vidange',
                'valeur' => 'Bonjour {{client}}, rappel : la vidange de votre véhicule {{immatriculation}} est à prévoir. Prenez rendez-vous. – {{atelier}}',
                'type' => 'text',
                'groupe' => 'notifications',
                'libelle' => 'Template – Rappel vidange',
            ],
        ];

        foreach ($params as $p) {
            Parametre::updateOrCreate(['cle' => $p['cle']], $p);
        }
    }
}
