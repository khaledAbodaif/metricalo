<?php

namespace App\Dto;

class HttpResponseDto
{

    private bool $status = true;
    private int $code = 200;
    private string $message = "Success!";
    private array $response = [];

    public function toArray() : array
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'message' => $this->message,
            'response' => $this->response,
        ];
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): int
    {
        return $this->message;
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setResponse(array $response): void
    {
        $this->response = $response;
    }

    public function setCode(int $code): void
    {
        $this->code = $code;
    }
}