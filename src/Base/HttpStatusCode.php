<?php

namespace ASP\Repository\Base;

/**
 * Class HttpStatusCode
 *
 * @package ASP\Repository\Base
 */
class HttpStatusCode
{
    /**
     * @var int
     */
    private $status;

    /**
     * @var bool
     */
    private $success;

    /**
     * @var string
     */
    private $message;

    public function __construct(int $status, bool $success, string $message)
    {
        $this->status = $status;
        $this->success = $success;
        $this->message = $message;
    }

    /**
     * Generic getter
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}
