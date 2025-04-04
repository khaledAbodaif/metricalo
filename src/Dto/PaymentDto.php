<?php

namespace App\Dto;

use App\Enum\PaymentMethodEnum;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentDto
{

    #[Assert\NotBlank]
    #[Groups(['read','write'])]
    #[Assert\Choice(callback: [PaymentMethodEnum::class,'toArray'], message: "Invalid payment method")]
    private string $method;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['read','write'])]
    private ?float $amount;

    #[Assert\NotBlank]
    #[Assert\Length(exactly: 3)]
    #[Groups(['read','write'])]
    private string $currency;

    #[Groups(['write'])]
    #[Assert\NotBlank]
    #[Assert\Luhn(message: "Invalid credit card number")]
    #[Assert\Length(min: 13, max: 19)]
    private string $cardNumber;

    #[Groups(['write'])]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 12)]
    private int $cardExpMonth;

    #[Groups(['write'])]
    #[Assert\NotBlank]
    #[Assert\Range(min: 2023, max: 2100)]
    private int $cardExpYear;

    #[Groups(['write'])]
    #[Assert\NotBlank]
    #[Assert\Length(exactly: 3)]
    #[Assert\Regex("/^\d{3}$/", message: "CVV must be a 3-digit number.")]
    private string $cardCvv;

    #[Groups(['read'])]
    private ?string $transactionId;

    #[Groups(['read'])]
    private ?\DateTimeInterface $dateOfCreating;


    #[Groups(['read'])]
    private ?string $cardBin;

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
    public function getCardExpMonth(): int
    {
        return $this->cardExpMonth;
    }

    public function setCardExpMonth(int $cardExpMonth): void
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

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }
    public function setTransactionId(?string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }
    public function getDateOfCreating(): ?\DateTimeInterface
    {
        return $this->dateOfCreating;
    }
    public function setDateOfCreating(?\DateTimeInterface $dateOfCreating): void
    {
        $this->dateOfCreating = $dateOfCreating;
    }
    public function getCardBin(): ?string
    {
        return $this->cardBin;
    }
    public function setCardBin(?string $cardBin): void
    {
        $this->cardBin = $cardBin;
    }
}