<?php

namespace App\Controller\Api\V1;

use App\Enum\PaymentMethodEnum;
use App\Helper\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for handling payment method related API endpoints.
 */
#[Route('/api/v1/payment/methods', name: 'api_v1_payment_methods')]
class PaymentMethodController extends AbstractController
{

    /**
     * Lists all available payment methods.
     *
     * @return Response The JSON response containing the list of payment methods.
     */
    #[Route('', name: 'index')]
    public function list(): Response
    {
        return ApiResponse::data(PaymentMethodEnum::toArray(true));
    }

}