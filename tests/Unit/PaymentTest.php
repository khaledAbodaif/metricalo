<?php

namespace App\Tests\Unit;

use App\Contract\IPaymentInterface;
use App\Dto\PaymentDto;
use App\Enum\PaymentMethodEnum;
use App\Service\AciPaymentService;
use App\Service\PaymentFactory;
use App\Service\Shift4PaymentService;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    private IPaymentInterface $aciPaymentService;

    private IPaymentInterface $shift4PaymentService;

    protected function setUp(): void
    {
        $this->aciPaymentService = new AciPaymentService();
        $this->shift4PaymentService = new Shift4PaymentService();
    }


    public function testValidAciPayment()
    {

        $successfulPaymentDto = new PaymentDto();
        $successfulPaymentDto->setMethod(PaymentMethodEnum::ACI);
        $successfulPaymentDto->setAmount(100.0);
        $successfulPaymentDto->setCurrency('USD');
        $successfulPaymentDto->setCardNumber('4242424242424242');
        $successfulPaymentDto->setCardExpMonth('12');
        $successfulPaymentDto->setCardExpYear(2030);
        $successfulPaymentDto->setCardCvv('123');

        $service = $this->aciPaymentService
            ->init($successfulPaymentDto)
            ->pay();


        $this->assertTrue($service->getResponse()->getStatus());

    }

    public function testInValidAciPayment()
    {

        $successfulPaymentDto = new PaymentDto();
        $successfulPaymentDto->setMethod(PaymentMethodEnum::ACI);
        $successfulPaymentDto->setAmount(100.0);
        $successfulPaymentDto->setCurrency('USD');
        $successfulPaymentDto->setCardNumber('11111');
        $successfulPaymentDto->setCardExpMonth('00');
        $successfulPaymentDto->setCardExpYear(2030);
        $successfulPaymentDto->setCardCvv('123');

        $service = $this->aciPaymentService
            ->init($successfulPaymentDto)
            ->pay();


        $this->assertFalse($service->getResponse()->getStatus());

    }

    public function testValidShift4Payment()
    {

        $successfulPaymentDto = new PaymentDto();
        $successfulPaymentDto->setMethod(PaymentMethodEnum::ACI);
        $successfulPaymentDto->setAmount(100.0);
        $successfulPaymentDto->setCurrency('USD');
        $successfulPaymentDto->setCardNumber('4242424242424242');
        $successfulPaymentDto->setCardExpMonth('12');
        $successfulPaymentDto->setCardExpYear(2030);
        $successfulPaymentDto->setCardCvv('123');

        $service = $this->shift4PaymentService
            ->init($successfulPaymentDto)
            ->pay();


        $this->assertTrue($service->getResponse()->getStatus());

    }

    public function testInValidShift4Payment()
    {

        $successfulPaymentDto = new PaymentDto();
        $successfulPaymentDto->setMethod(PaymentMethodEnum::ACI);
        $successfulPaymentDto->setAmount(100.0);
        $successfulPaymentDto->setCurrency('USD');
        $successfulPaymentDto->setCardNumber('11111');
        $successfulPaymentDto->setCardExpMonth('00');
        $successfulPaymentDto->setCardExpYear(2030);
        $successfulPaymentDto->setCardCvv('123');

        $service = $this->shift4PaymentService
            ->init($successfulPaymentDto)
            ->pay();


        $this->assertFalse($service->getResponse()->getStatus());

    }

}