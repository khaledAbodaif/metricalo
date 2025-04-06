<?php

namespace App\Event;

use App\Dto\PaymentResponseDto;
use Symfony\Contracts\EventDispatcher\Event;

class PaymentFailedEvent extends Event
{
    public const NAME = 'payment.failed';

    public function __construct(
        public PaymentResponseDto $paymentResponseDto,
    ) {}

}