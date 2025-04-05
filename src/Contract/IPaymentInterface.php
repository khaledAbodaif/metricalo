<?php

namespace App\Contract;

use App\Dto\PaymentDto;
use App\Dto\PaymentResponseDto;

interface IPaymentInterface
{

    public function init(PaymentDto $payment): self;
    public function pay(): PaymentResponseDto;
}