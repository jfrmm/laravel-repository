<?php

return [
    'success' => [
        'index' => ':Entity listada com sucesso',
        'create' => ':Entity criada com sucesso',
        'read' => ':Entity mostrada com sucesso',
        'update' => ':Entity actualizada com sucesso',
        'delete' => ':Entity eliminada com sucesso',
        'clear' => ':Entity limpa com sucesso',
        'enable' => ':Entity activada com sucesso',
        'disable' => ':Entity desactivada com sucesso',
        'sync' => ':Entity sincronizada com sucesso',
        'email_sent' => 'Email enviado com sucesso',

        'template' => 'Obtida localização do template de :Entity',
        'import' => 'Importado ficheiro de :Entity. Importados :count registos',

        /**
         * Exports
         */
        'export' => 'Exportada lista de :Entity com sucesso',
        'review_report' => 'Relatório de :Entity gerado com sucesso',
    ],

    'error' => [
        'index' => 'Erro ao listar :Entity',
        'create' => 'Erro ao criar :Entity',
        'read' => 'Erro ao mostrar :Entity',
        'update' => 'Erro ao actualizar :Entity',
        'delete' => 'Erro ao eliminar :Entity',
        'enable' => 'Erro ao activar :Entity',
        'disable' => 'Erro ao desactivar :Entity',

        'forbidden' => 'Utilizador não autorizado nesta acção',

        'sync' => 'Erro ao sincronizar :Entity',
        'not_found' => ':Entity não encontrado',
        'is_disable' => ':Entity associado está desactivado',

        'validation_failed' => 'Validação falhou',
    ],
];
