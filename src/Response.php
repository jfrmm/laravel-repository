<?php

namespace ASP\Repository;

/**
 * @package ASP\Repository
 */
abstract class Response
{
    public const DATA = 'data';
    public const DISMISSIBLE = 'dismissible';
    public const ERRORS = 'errors';
    public const MESSAGE = 'message';
    public const PAGINATION = 'pagination';
    public const RESPONSES = 'responses';
    public const STATUS = 'status';
    public const SUCCESS = 'success';

    /**
     * The JSON structure of a success response
     */
    public const JSON_SUCCESS = [
        'status',
        'success',
        'data',
        'message',
    ];

    /**
     * The JSON structure of a response's pagination data
     */
    public const JSON_PAGINATION = [
        'pagination' => [
            'current_page',
            'page_size',
            'last_page',
            'total',
        ],
    ];
}
