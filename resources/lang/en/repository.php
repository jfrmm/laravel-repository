<?php

return [
    'success' => [
        'index' => ':Entity successfully listed',
        'create' => ':Entity successfully created',
        'read' => ':Entity successfully shown',
        'update' => ':Entity successfully updated',
        'delete' => ':Entity successfully deleted',
        'clear' => ':Entity successfully cleared',
        'enable' => ':Entity successfully enabled',
        'disable' => ':Entity successfully disabled',
        'sync' => ':Entity successfully synced',
        'email_sent' => 'Email have been sent successfully',

        'template' => 'Got :Entity template location',
        'import' => 'Got :Entity file imported. Imported :count records',

        /**
         * Exports
         */
        'export' => 'Successfully exported :Entity list',
        'review_report' => ':Entity Report generated with success',
    ],

    'error' => [
        'index' => 'Error listing :Entity',
        'create' => 'Error creating :Entity',
        'read' => 'Error showing :Entity',
        'update' => 'Error updating :Entity',
        'delete' => 'Error deleting :Entity',
        'enable' => 'Error enabling :Entity',
        'disable' => 'Error disabling :Entity',

        'forbidden' => 'User cannot perform this action',

        'sync' => 'Error syncing :Entity',
        'not_found' => ':Entity not found',
        'is_disable' => 'Associated :Entity is disabled',

        'validation_failed' => 'Validation failed',
    ],
];
