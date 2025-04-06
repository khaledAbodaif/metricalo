<?php

namespace App\Event;

use App\Dto\PaymentResponseDto;
use Symfony\Contracts\EventDispatcher\Event;

class PaymentSuccessEvent extends Event
{
    public const NAME = 'payment.success';

    public function __construct(
        public PaymentResponseDto $paymentResponseDto,
    ) {}

}