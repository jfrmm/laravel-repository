<?php

namespace ASP\Repository\Serializers;

use ASP\Repository\Response;
use Flugg\Responder\Serializers\ErrorSerializer as FluggErrorSerializer;

/**
 * Class ErrorSerializer
 *
 * @package ASP\Repository\Serializers
 */
class ErrorSerializer extends FluggErrorSerializer
{
    /**
     * Format the error data.
     *
     * @param  mixed|null  $errorCode
     * @param  string|null $message
     * @param  array|null  $data
     *
     * @return array
     */
    public function format($errorCode = null, string $message = null, array $data = null): array
    {
        $response = [
            Response::STATUS => $errorCode,
            Response::MESSAGE => $message,
        ];

        if (! is_null($data)) {
            if (isset($data[Response::DISMISSIBLE])) {
                $response[Response::DISMISSIBLE] = $data[Response::DISMISSIBLE];
                unset($data[Response::DISMISSIBLE]);
            }
            $response[Response::DATA] = $data;
        }

        return $response;
    }
}
