<?php

namespace App\Tests\Unit;

use App\Service\AciPaymentService;
use App\Service\Shift4PaymentService;
use PHPUnit\Framework\TestCase;
use App\Enum\PaymentMethodEnum;
use App\Service\PaymentFactory;

class FactoryTest extends TestCase
{
    private PaymentFactory $paymentFactory;

    protected function setUp(): void
    {
        $this->paymentFactory = new PaymentFactory();
    }

    public function testGetReturnsAciPaymentService(): void
    {
        $service = $this->paymentFactory->get(PaymentMethodEnum::ACI);
        $this->assertInstanceOf(AciPaymentService::class, $service);
    }

    public function testGetReturnsShift4PaymentService(): void
    {
        $service = $this->paymentFactory->get(PaymentMethodEnum::SHIFT4);
        $this->assertInstanceOf(Shift4PaymentService::class, $service);
    }

}