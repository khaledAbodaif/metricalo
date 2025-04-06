<?php

namespace App\Service;

use App\Contract\IPaymentInterface;
use App\Dto\HttpResponseDto;
use App\Dto\PaymentDto;
use App\Dto\PaymentResponseDto;
use App\Helper\HttpHelper;

class AciPaymentService implements IPaymentInterface
{

    private const URL = 'https://eu-test.oppwa.com/v1';
    private const CHARGE_URL = self::URL . '/payments';
    private const ENTITY_ID = "8ac7a4c79394bdc801939736f17e063d";
    private const TOKEN = 'OGFjN2E0Yzc5Mzk0YmRjODAxOTM5NzM2ZjFhNzA2NDF8enlac1lYckc4QXk6bjYzI1NHNng=';
    private const BRAND = 'VISA';


    private PaymentDto $paymentDto;
    private PaymentResponseDto $paymentResponseDto;


    public function init(PaymentDto $payment): IPaymentInterface
    {
        $this->paymentDto = $payment;
        $this->paymentResponseDto = new PaymentResponseDto();
        return $this;
    }

    public function prepareChargePayload(): void
    {
        $this->paymentResponseDto->setPayload([
            'entityId' => self::ENTITY_ID,
            'amount' => $this->paymentDto->getAmount(),
            'currency' =>  $this->paymentDto->getCurrency(),
            'paymentBrand' => self::BRAND,
            'paymentType' => 'DB',
            'card.number' => $this->paymentDto->getCardNumber(),
            'card.holder' => 'Jane Jones',
            'card.expiryMonth' => $this->paymentDto->getCardExpMonth(),
            'card.expiryYear' => $this->paymentDto->getCardExpYear(),
            'card.cvv' => $this->paymentDto->getCardCvv(),


        ]);

    }


    public function prepareChargeResponse(HttpResponseDto $response): void
    {

        $this->paymentResponseDto->setResponse($response);

        if ($response->getStatus()) {
            // successful event
            $this->paymentResponseDto->setAmount($response->getResponse()['amount']);
            $this->paymentResponseDto->setCurrency($response->getResponse()['currency']);
            $this->paymentResponseDto->setTransactionId($response->getResponse()['id']);
            $this->paymentResponseDto->setDateOfCreating( new \DateTime($response->getResponse()['timestamp']));
            $this->paymentResponseDto->setCardBin($response->getResponse()['card']['bin']);
        }


    }

    public function pay(): PaymentResponseDto
    {

        $this->prepareChargePayload();

        $response = HttpHelper::make()
            ->setUrl(self::CHARGE_URL)
            ->setHeaders([
                'Authorization' => 'Bearer ' . self::TOKEN,
                'Content-Type' => 'application/json',
            ])
            ->setPayload($this->paymentResponseDto->getPayload())
            ->post()
            ->getResponse();

        $this->prepareChargeResponse($response);

        return $this->paymentResponseDto;
    }
}