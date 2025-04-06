<?php

namespace App\Listener;

use App\Helper\ApiResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ValidationExceptionListener
{

    /**
     * Handles kernel exceptions and maps validation errors to a standardized API response.
     * For Validation Dtos exceptions and enum parameter
     * @param ExceptionEvent $event The event that contains the exception.
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        $validationException = null;
        $errors = [];

        if ($throwable instanceof ValidationFailedException) {
            $validationException = $throwable;
        } elseif ($throwable->getPrevious() instanceof ValidationFailedException) {
            $validationException = $throwable->getPrevious();
        } elseif ($throwable->getPrevious() instanceof \ValueError && str_contains($throwable->getMessage(), 'is not a valid backing value')) {
            $errors['parameter'] = "Please Enter a valid data";
            $event->setResponse(ApiResponse::error($errors, "Validation Error", 422));
        }

        if ($validationException) {
            foreach ($validationException->getViolations() as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }


            $event->setResponse(ApiResponse::error($errors, "Validation Error", 422));
        }

    }

}