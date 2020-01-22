<?php

return [
    'success' => [
        //
    ],
    'error' => [
        /**
         * Client errors
         */
        'bad_request' => 'Bad request.',
        'unauthorized' => 'Not authenticated.',
        'forbidden' => 'Not authorized on this action.',
        'not_found' => 'URL/resource not found, or using wrong HTTP verb.',
        'unprocessable_entity' => 'Request validation failed.',

        /**
         * Server errors
         */
        'internal_server_error' => 'An internal server error occurred.',
    ],
];
