<?php

/**
 * Paramètres de l'atelier (utilisés notamment pour les PDF).
 * À publier ou copier dans config/garage.php du projet Laravel.
 */

return [

    'adresse' => env('GARAGE_ADRESSE', 'Bamako, Mali'),
    'telephone' => env('GARAGE_TELEPHONE', '+223 70 00 00 00'),
    'email' => env('GARAGE_EMAIL', 'contact@garage-moderne.local'),
    'nif' => env('GARAGE_NIF', ''),
    'rc' => env('GARAGE_RC', ''),

    // Validité des devis (jours)
    'devis_validite_jours' => (int) env('GARAGE_DEVIS_VALIDITE', 15),

];
