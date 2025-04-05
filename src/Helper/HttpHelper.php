<?php

namespace App\Helper;

use App\Dto\HttpResponseDto;
use Symfony\Component\HttpClient\HttpClient;

class HttpHelper
{

    private string $url;
    private array $payload;
    private array $headers;

    private HttpResponseDto $httpResponse;

    private function __construct()
    {
        $this->httpResponse = new HttpResponseDto();
    }

    public static function make(): self
    {
        return new self();
    }

    public function post(): self
    {

        try {
            $httpClient = HttpClient::create();

            $this->httpResponse->setResponse($httpClient->request('POST', $this->url, [
                'body' => $this->payload,
                'headers' => $this->headers
            ])->toArray());

        } catch (\Exception $e) {

            $this->httpResponse->setStatus(false);
            $this->httpResponse->setMessage($e->getMessage());
            $this->httpResponse->setCode($e->getCode());

        }

        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function setPayload(array $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function getResponse(): HttpResponseDto
    {
        return $this->httpResponse;
    }
}