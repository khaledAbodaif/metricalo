<?php

namespace App\Exception;

use Psr\Log\LoggerInterface;

class PaymentException extends \RuntimeException
{

    /**
     * @param string $message Exception message
     * @param int $code Exception code (default: 0)
     * @param \Throwable|null $previous Previous exception if nested
     */
    public function __construct(
        string $message = "An error occurred while parsing payment data",
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}