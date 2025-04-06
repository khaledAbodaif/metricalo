<?php

namespace App\Contract;

use App\Dto\HttpResponseDto;
use App\Dto\PaymentDto;
use App\Dto\PaymentResponseDto;


interface IPaymentInterface
{


    /**
     * Initializes the payment service with the given payment data.
     * Handling if the payment has custom initialization.
     *
     * @param PaymentDto $payment The payment data transfer object.
     * @return self Returns the instance of the payment service.
     */
    public function init(PaymentDto $payment): self;


    /**
     * Processes the payment request.
     * This method should be implemented to handle the actual payment processing.
     *
     * @return PaymentResponseDto Returns the payment response data transfer object.
     */
    public function pay(): PaymentResponseDto;

    /**
     * Prepares the charge response from the payment service.
     * This method should be implemented to handle the response from the payment request.
     *
     * @param HttpResponseDto $response The HTTP response data transfer object.
     * @return void
     */
    public function prepareChargeResponse(HttpResponseDto $response): void;

    /**
     * Prepares the charge payload for the payment service.
     * This method should be implemented to prepare the payload for the payment request.
     * Allowing OC principle if we want charge with card token.
     * @return void
     */
    public function prepareChargePayload(): void;


}