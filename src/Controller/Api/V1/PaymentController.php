<?php

namespace App\Controller\Api\V1;

use App\Dto\PaymentDto;
use App\Helper\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/payments', name: 'api_v1_payments')]
class PaymentController extends AbstractController
{

    public function __construct(
        private readonly SerializerInterface $serializer ,
        private readonly ValidatorInterface $validator
    )
    {
    }

    #[Route('/{method}', name: 'payments', methods: ['POST'])]
    public function store(string $method , Request $request ):JsonResponse
    {
        // map to dto and Validate payment request
        $paymentDto = $this->serializer->deserialize($request->getContent(), PaymentDto::class, 'json',['groups' => ['write']]);
        $paymentDto->setMethod($method);

        // validate
        $errors = $this->validator->validate($paymentDto);
        if ($errors->count() > 0) {
            return ApiResponse::error($errors);
        }

        // map to payment response dto
        $data = $this->serializer->serialize($paymentDto, 'json', ['groups' => ['read']]);
        return ApiResponse::data($data,"Payment processed successfully");

    }
}