<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class PaymentResponseDto
{
    #[Groups(['read'])]
    private ?float $amount;

    #[Groups(['read'])]
    private string $currency;

    #[Groups(['read'])]
    private ?string $transactionId;

    #[Groups(['read'])]
    private \DateTimeInterface $dateOfCreating;

    #[Groups(['read'])]
    private ?string $cardBin;
    private ?array $payload;
    private ?HttpResponseDto $response;


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
    public function getDateOfCreating(): \DateTimeInterface
    {
        return $this->dateOfCreating;
    }
    public function setDateOfCreating(\DateTimeInterface $dateOfCreating): void
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

    public function getResponse(): ?HttpResponseDto
    {
        return $this->response;
    }

    public function setResponse(?HttpResponseDto $response): void
    {
        $this->response = $response;
    }
}