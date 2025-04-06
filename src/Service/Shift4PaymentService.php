<?php

namespace App\Service;

use App\Contract\IPaymentInterface;
use App\Dto\HttpResponseDto;
use App\Dto\PaymentDto;
use App\Dto\PaymentResponseDto;
use App\Exception\PaymentException;
use App\Helper\HttpHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Log\Logger;

class Shift4PaymentService implements IPaymentInterface
{
    const TOKEN = 'pr_test_tXHm9qV9qV9bjIRHcQr9PLPa';
    const PASSWORD = '';

    const URL = 'https://api.shift4.com';
    const CHARGE_URL = self::URL . '/charges';


    private PaymentDto $paymentDto;
    private PaymentResponseDto $paymentResponseDto;
    private LoggerInterface $logger;


    public function init(PaymentDto $payment): IPaymentInterface
    {
        $this->paymentDto = $payment;
        $this->paymentResponseDto = new PaymentResponseDto();
        $this->logger = new Logger();

        return $this;
    }


    // to allow OC principle if we want charge with card token
    public function prepareChargePayload(): void
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

    public function prepareChargeResponse(HttpResponseDto $response): void
    {

        $this->paymentResponseDto->setResponse($response);

        if ($response->getStatus()) {
            // successful event
            $this->paymentResponseDto->setAmount($response->getResponse()['amount']);
            $this->paymentResponseDto->setCurrency($response->getResponse()['currency']);
            $this->paymentResponseDto->setTransactionId($response->getResponse()['id']);
            $this->paymentResponseDto->setDateOfCreating(new \DateTime("@" . $response->getResponse()['created'] . ""));
            $this->paymentResponseDto->setCardBin($response->getResponse()['card']['first6']);
        }else{
            $this->logger->error('AciPaymentService:pay:http', [
                'payload' => $this->paymentResponseDto->getPayload(),
                'response' => $this->paymentResponseDto->getResponse()
            ]);
        }


    }

    public function pay(): PaymentResponseDto
    {

        try {
            $this->prepareChargePayload();

            $response = HttpHelper::make()
                ->setUrl(self::CHARGE_URL)
                ->setHeaders([
                    'Authorization' => 'Basic ' . base64_encode(self::TOKEN . ":" . self::PASSWORD),
                    'Content-Type' => 'application/json',
                ])
                ->setPayload($this->paymentResponseDto->getPayload())
                ->post()
                ->getResponse();


            $this->prepareChargeResponse($response);
        } catch (\Exception $exception) {
            error_log('AciPaymentService:pay:internal', [
                'exception' => $exception->getMessage(),
                'payload' => $this->paymentResponseDto->getPayload(),
                'response' => $this->paymentResponseDto->getResponse()
            ]);

            throw new PaymentException("An error occurred while parsing payment data : " . $exception->getMessage());
        }


        return $this->paymentResponseDto;
    }
}