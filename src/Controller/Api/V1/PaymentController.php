<?php

namespace App\Controller\Api\V1;

use App\Dto\PaymentDto;
use App\Enum\PaymentMethodEnum;
use App\Helper\ApiResponse;
use App\Service\PaymentFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/v1/payments', name: 'api_v1_payments')]
class PaymentController extends AbstractController
{

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly PaymentFactory      $paymentFactory
    )
    {
    }

    #[Route('/{method}', name: 'payments', methods: ['POST'])]
    public function store(PaymentMethodEnum $method, #[MapRequestPayload] PaymentDto $paymentDto): JsonResponse
    {
        $paymentDto->setMethod($method);

        try {

            $paymentResponse = $this->paymentFactory->get($method)->init($paymentDto)->pay();

            $data = $this->serializer->normalize($paymentResponse, null, ['groups' => ['read']]);
            return ApiResponse::data($data, "Payment processed successfully");

        } catch (\Throwable $e) {

            return ApiResponse::error([], "Payment failed !", Response::HTTP_FAILED_DEPENDENCY);
        }


    }
}