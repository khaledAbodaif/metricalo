<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class PaymentResponseDto
{
    #[Groups(['read'])]
    private ?float $amount;

    #[Groups(['read'])]
    private string $currency;

    #[Groups(['read'])]
    private ?string $transactionId;

    #[Groups(['read'])]
    private ?int $dateOfCreating;

    #[Groups(['read'])]
    private ?string $cardBin;
    private ?array $payload;


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

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }
    public function setTransactionId(?string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }
    public function getDateOfCreating(): ?int
    {
        return $this->dateOfCreating;
    }
    public function setDateOfCreating(?int $dateOfCreating): void
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

    public function getPayload(): ?array
    {
        return $this->payload;
    }
    public function setPayload(?array $payload): void
    {
        $this->payload = $payload;
    }

}