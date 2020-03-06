<?php

return [
    'success' => [
        //
    ],
    'error' => [
        //  Client errors
        'bad_request' => 'Mauvaise demande.',
        'unauthorized' => 'Non authentifié.',
        'forbidden' => 'Non autorisé sur cette action.',
        'not_found' => 'URL / ressource introuvable ou utilisant un verbe HTTP incorrect.',
        'unprocessable_entity' => 'Échec de la validation de la demande.',
        // Server errors
        'internal_server_error' => 'Une erreur de serveur interne s\'est produite.',
    ],
];
