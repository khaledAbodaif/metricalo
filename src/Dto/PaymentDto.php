<?php

namespace App\Dto;

use App\Enum\PaymentMethodEnum;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentDto
{

    private PaymentMethodEnum $method;

    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?float $amount;

    #[Assert\NotBlank]
    #[Assert\Length(exactly: 3)]
    private string $currency;

    #[Assert\Luhn(message: "Invalid credit card number")]
    #[Assert\Length(min: 13, max: 19)]
    private string $cardNumber = "4242424242424242";

    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 12)]
    private string $cardExpMonth;

    #[Assert\NotBlank]
    #[Assert\Range(min: 2023, max: 2100)]
    private int $cardExpYear;

    #[Assert\NotBlank]
    #[Assert\Length(exactly: 3)]
    #[Assert\Regex("/^\d{3}$/", message: "CVV must be a 3-digit number.")]
    private string $cardCvv;



    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }
    public function setCardNumber(string $cardNumber): void
    {
        $this->cardNumber = $cardNumber;
    }

    public function getCardExpYear(): int
    {
        return $this->cardExpYear;
    }

    public function setCardExpYear(int $cardExpYear): void
    {
        $this->cardExpYear = $cardExpYear;
    }
    public function getCardExpMonth(): string
    {
        return $this->cardExpMonth;
    }

    public function setCardExpMonth(string $cardExpMonth): void
    {
        $this->cardExpMonth = $cardExpMonth;
    }
    public function getCardCvv(): ?string
    {
        return $this->cardCvv;
    }
    public function setCardCvv(string $cardCvv): void
    {
        $this->cardCvv = $cardCvv;
    }

    public function getMethod(): PaymentMethodEnum
    {
        return $this->method;
    }

    public function setMethod(PaymentMethodEnum $method): void
    {
        $this->method = $method;
    }

}