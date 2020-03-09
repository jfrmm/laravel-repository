<?php

return [
    'success' => [
        'index' => 'Tous les enregistrements récupérés',
        'create' => ':Entity créée avec succès',
        'read' => ':Entity affichée avec succès',
        'update' => ':Entity mise à jour avec succès',
        'delete' => ':Entity supprimée avec succès',
        'clear' => ':Entity effacée avec succès',
        'enable' => ':Entity activée avec succès',
        'disable' => ':Entity désactivée avec succès',
        'sync' => ':Entity synchronisée avec succès',
        'email_sent' => 'Email envoyé',

        'template' => 'Eu :Entity emplacement du modèle.',
        'import' => 'Fichier de :Entity importé. Importé :count valeurs.',

        /**
         * Exports
         */
        'export' => 'Liste des :Entity exportée avec succès',
        'review_report' => ':Entity Report generated with success.',
    ],

    'error' => [
        'index' => 'Erreur de référencement :Entity',
        'create' => 'Erreur création :Entity',
        'read' => 'Erreur affichage :Entity',
        'update' => 'Erreur suppression :Entity',
        'delete' => 'Erreur mise à jour :Entity',
        'enable' => 'Erreur activation :Entity',
        'disable' => 'Erreur désactivation :Entity',

        'forbidden' => 'L\'utilisateur ne peut pas exécuter cette action',

        'sync' => 'Erreur synchronisation :Entity',
        'not_found' => ':Entity non trouvée',
        'is_disabled' => 'Associé :Entity est désactivée.',

        'validation_failed' => 'Validation échouée',
    ],
];
