<?php

namespace App\Service;

use App\Contract\IPaymentInterface;
use App\Dto\HttpResponseDto;
use App\Dto\PaymentDto;
use App\Dto\PaymentResponseDto;
use App\Helper\HttpHelper;

class Shift4PaymentService implements IPaymentInterface
{
    const TOKEN = 'pr_test_tXHm9qV9qV9bjIRHcQr9PLPa';
    const PASSWORD = '';

    const URL = 'https://api.shift4.com';
    const CHARGE_URL = self::URL . '/charges';


    private PaymentDto $paymentDto;
    private PaymentResponseDto $paymentResponseDto;


    public function init(PaymentDto $payment): IPaymentInterface
    {
        $this->paymentDto = $payment;
        $this->paymentResponseDto = new PaymentResponseDto();
        return $this;
    }


    // to allow OC principle if we want charge with card token
    private function prepareChargePayload() : void
    {
        $this->paymentResponseDto->setPayload([
            'amount' => $this->paymentDto->getAmount(),
            'currency' => $this->paymentDto->getCurrency(),
            'card' => [
                'number' => $this->paymentDto->getCardNumber(),
                'expMonth' => $this->paymentDto->getCardExpMonth(),
                'expYear' => $this->paymentDto->getCardExpYear(),
                'cvc' => $this->paymentDto->getCardCvv(),
            ]
        ]);

    }

    private function prepareChargeResponse(HttpResponseDto $response): void
    {

        $this->paymentResponseDto->setResponse($response);

        if ($response->getStatus()){
            // successful event
            $this->paymentResponseDto->setAmount($response->getResponse()['amount']);
            $this->paymentResponseDto->setCurrency($response->getResponse()['currency']);
            $this->paymentResponseDto->setTransactionId($response->getResponse()['id']);
            $this->paymentResponseDto->setDateOfCreating($response->getResponse()['created']);
            $this->paymentResponseDto->setCardBin($response->getResponse()['card']['first6']);
        }


    }
    public function pay(): PaymentResponseDto
    {

        $this->prepareChargePayload();

        $response = HttpHelper::make()
            ->setUrl(self::CHARGE_URL)
            ->setHeaders([
                'Authorization' => 'Basic ' . base64_encode(self::TOKEN .":".self::PASSWORD),
                'Content-Type' => 'application/json',
            ])
            ->setPayload($this->paymentResponseDto->getPayload())
            ->post()
            ->getResponse();


        $this->prepareChargeResponse($response);


        return $this->paymentResponseDto;
    }
}