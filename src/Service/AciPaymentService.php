<?php

namespace App\Service;

use App\Contract\IPaymentInterface;
use App\Dto\HttpResponseDto;
use App\Dto\PaymentDto;
use App\Dto\PaymentResponseDto;
use App\Event\PaymentFailedEvent;
use App\Event\PaymentSuccessEvent;
use App\Exception\PaymentException;
use App\Helper\HttpHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AciPaymentService implements IPaymentInterface
{

    private const URL = 'https://eu-test.oppwa.com/v1';
    private const CHARGE_URL = self::URL . '/payments';
    private const ENTITY_ID = "8ac7a4c79394bdc801939736f17e063d";
    private const TOKEN = 'OGFjN2E0Yzc5Mzk0YmRjODAxOTM5NzM2ZjFhNzA2NDF8enlac1lYckc4QXk6bjYzI1NHNng=';
    private const BRAND = 'VISA';


    private PaymentDto $paymentDto;
    private PaymentResponseDto $paymentResponseDto;
    private LoggerInterface $logger;
    private EventDispatcherInterface $dispatcher;



    public function init(PaymentDto $payment): IPaymentInterface
    {
        $this->paymentDto = $payment;
        $this->paymentResponseDto = new PaymentResponseDto();
        $this->logger = new Logger();
        $this->dispatcher = new EventDispatcher();

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

            $this->dispatcher->dispatch(
                new PaymentSuccessEvent(
                    $this->paymentResponseDto
                ),
                PaymentSuccessEvent::NAME
            );

        }else{
            $this->logger->error('AciPaymentService:pay:http', [
                'payload' => $this->paymentResponseDto->getPayload(),
                'response' => $this->paymentResponseDto->getResponse()
            ]);

            $this->dispatcher->dispatch(
                new PaymentFailedEvent(
                    $this->paymentResponseDto
                ),
                PaymentFailedEvent::NAME
            );
        }


    }

    public function pay(): PaymentResponseDto
    {

        try {

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
        }catch (\Exception $exception){
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