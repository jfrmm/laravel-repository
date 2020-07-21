<?php

return [
    'success' => [
        //
    ],
    'error' => [
        /**
         * Client errors
         */
        'bad_request' => 'Pedido incorrecto.',
        'unauthorized' => 'Não autenticado.',
        'forbidden' => 'Não autorizado nesta acção.',
        'not_found' => 'URL/recurso não encontrado, ou verbo HTTP incorrecto.',
        'unprocessable_entity' => 'Validação do pedido falhou.',

        /**
         * Server errors
         */
        'internal_server_error' => 'Aconteceu um erro interno no servidor.',
    ],
];
