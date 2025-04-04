<?php

namespace App\Contract;

use App\Dto\PaymentDto;

interface IPaymentInterface
{

    public function init(): self;
    public function pay(): PaymentDto;
}