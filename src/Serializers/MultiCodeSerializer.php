<?php

namespace ASP\Repository\Serializers;

use ASP\Repository\Response;
use Flugg\Responder\Http\MakesResponses;
use Flugg\Responder\Serializers\SuccessSerializer as FluggSuccessSerializer;

/**
 * Class MultiCodeSerializer
 *
 * @package ASP\Repository\Serializers
 */
class MultiCodeSerializer extends FluggSuccessSerializer
{
    use MakesResponses;

    /**
     * Format the error data.
     *
     * @param null        $status
     * @param string|null $message
     * @param array|null  $data
     *
     * @return array
     */
    public function format($status = null, string $message = null, $data = null): array
    {
        $responses = [];
        foreach ($data[Response::RESPONSES] as $response) {
            if ($response[Response::SUCCESS] === true) {
                $responses[] = $this->success($response[Response::DATA], null, null)
                    ->meta([Response::MESSAGE => $response[Response::MESSAGE]])->toJson();
            } else {
                $errorResponse = $this->error($response[Response::STATUS], $response[Response::MESSAGE]);
                if (isset($response[Response::DATA])) {
                    $errorResponse = $errorResponse->data($response[Response::DATA]);
                }
                $responses[] = $errorResponse
                    ->toJson();
            }
        }
        return [
            Response::STATUS => $status,
            Response::MESSAGE => $message,
            Response::RESPONSES => $responses
        ];
    }
}
