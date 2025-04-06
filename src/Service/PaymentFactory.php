<?php

namespace App\Service;

use App\Contract\IPaymentInterface;
use App\Enum\PaymentMethodEnum;

class PaymentFactory
{

    /**
     *
     * get the payment class that the user want
     * if not exist return ex
     *  the enum name reflect to payment class name
     *  for example ACI the class is AciPaymentService
     *  for example PAYMENT_NAME the class is PaymentNamePaymentService
     *
     * @param PaymentMethodEnum $method
     * @return \Exception|IPaymentInterface
     * @throws \Exception
     */

    public function get(PaymentMethodEnum $method): IPaymentInterface|\Exception
    {

        $className = 'App\\Service\\' . $this->formatString($method->name) . 'PaymentService';
        if (class_exists($className))
            return new $className();

        throw new \Exception("Invalid gateway");
    }

    /*
     * converter to convert method enum name to class name
     * the standard is to have the class name start with capital letter
     * */
    private function formatString($input): string
    {
        return str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $input))));
    }

}