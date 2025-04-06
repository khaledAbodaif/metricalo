<?php

namespace App\Command;

use App\Dto\PaymentDto;
use App\Enum\PaymentMethodEnum;
use App\Service\PaymentFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class StorePaymentCommand
 * @package App\Command
 *
 * This command allows you to process a payment through different payment methods.
 * You can specify the payment method and provide a JSON payload with payment details.
 * If the payload is not provided, you will be prompted to enter payment details interactively.
 */
#[AsCommand(
    name: 'app:store-payment',
    description: 'Process a payment through different payment methods',
)]
class StorePaymentCommand extends Command
{

    public function __construct(
        private PaymentFactory      $paymentFactory,
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator
    )
    {
        parent::__construct();
    }

    /**
     * Configure the command options and arguments.
     */
    protected function configure(): void
    {

        $this
            ->addArgument(
                'method',
                InputArgument::REQUIRED,
                'The payment method to use ' . implode('|', PaymentMethodEnum::toArray())
            )
            ->addArgument(
                'payload',
                InputArgument::OPTIONAL,
                'JSON payload containing payment data ex: "{"amount":92.00,"currency":"EUR","cardNumber":"4242424242424242","cardExpYear":2030,"cardExpMonth":12,"cardCvv":"123"}'
            )
            ->setDescription('Process a payment through different payment methods')
            ->setHelp('method parameter required then the payment payload is optional. If not provided, you will be prompted to enter payment details interactively.');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {

            // 1. Validate and parse the payment method argument
            $method = $this->validatePaymentMethod($input->getArgument('method'));

            // 2. Validate and parse the payload
            $payload = $this->validatePayload($io, $input->getArgument('payload'));

            // 3. Deserialize the payload into a PaymentDto object
            $paymentDto = $this->validateAndCreateDto($payload);

            // 4. Pay
            return $this->processPayment($io, $method, $paymentDto);

        } catch (\RuntimeException $exception) {

            // Handle any exceptions that occur during validation or processing
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Validate the payment method and return the corresponding enum value.
     *
     * @throws \RuntimeException
     */
    private function validatePaymentMethod(?string $method): PaymentMethodEnum
    {

        try {
            return PaymentMethodEnum::from($method);
        } catch (\ValueError $e) {
            throw new \RuntimeException(sprintf(
                'Invalid payment method "%s". Must be one of: %s',
                $method,
                implode(' | ', PaymentMethodEnum::toArray())
            ));
        }
    }

    /**
     * Validate the payload and return a JSON string.
     * Payload can be provided as a JSON string or interactively entered.
     * @throws \RuntimeException
     */

    private function validatePayload(SymfonyStyle $io, ?string $payload): string
    {

        if (!empty($payload)) {
            $this->validateJsonSyntax($payload);
        } else {

            $io->section('Please enter payment details');

            $payload = [

                'currency' => strtoupper($io->ask('Currency (3-letter code, e.g. USD)', null, function ($value) {
                    if (strlen($value) !== 3) {
                        throw new \RuntimeException('Currency must be 3 letters');
                    }
                    return $value;
                })),
                'amount' => $io->ask('Amount (e.g. 92.00)', null, function ($value) {
                    if (!is_numeric($value)) {
                        throw new \RuntimeException('Amount must be a number');
                    }
                    return (float)$value;
                }),
                'cardExpMonth' => $io->ask('cardExpMonth (e.g. 01.12)', null, function ($value) {
                    if (strlen($value) !== 2) {
                        throw new \RuntimeException('cardExpMonth must valid format');
                    }
                    return $value;
                }),
                'cardExpYear' => $io->ask('cardExpYear (e.g. 2030)', null, function ($value) {
                    if (!is_numeric($value) || strlen($value) !== 4 || $value < date('Y')) {
                        throw new \RuntimeException('Amount must be a number');
                    }
                    return (float)$value;
                }),
                'cardCvv' => $io->ask('cardCvv (e.g. 123)', null, function ($value) {
                    if ( strlen($value) !== 3) {
                        throw new \RuntimeException('cardCvv must be a number');
                    }
                    return $value;
                }),
                'cardNumber' => $io->ask('cardNumber (e.g. 4242424242424242)', null, function ($value) {
                    if (strlen($value) < 13 || strlen($value) > 19) {
                        throw new \RuntimeException('cardNumber must be a number');
                    }
                    return $value;
                }),
            ];

            $payload = json_encode($payload);
        }
        return $payload;
    }

    /**
     * Validate the JSON syntax of the payload.
     *
     * @throws \RuntimeException
     */
    private function validateJsonSyntax(?string $payload): void
    {
        json_decode($payload);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Invalid JSON payload: ' . json_last_error_msg());
        }
    }

    /**
     * Validate the payload and create a PaymentDto object.
     * deserialize the Payload and validate it with the dto.
     * @throws \RuntimeException
     */
    private function validateAndCreateDto(?string $payload): PaymentDto
    {

        $paymentDto = $this->deserializePayload($payload);

        $this->validateDto($paymentDto);

        return $paymentDto;
    }

    /**
     * Deserialize the payload into a PaymentDto object.
     *
     * @throws \RuntimeException
     */
    private function deserializePayload(string $payload): PaymentDto
    {
        try {
            /** @var PaymentDto $dto */
            $dto = $this->serializer->deserialize($payload, PaymentDto::class, 'json');
            return $dto;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to parse payload: ' . $e->getMessage());
        }
    }

    /**
     * Validate the PaymentDto object.
     *
     * @throws \RuntimeException
     */
    private function validateDto(PaymentDto $dto): void
    {
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = sprintf(
                    '[%s] %s',
                    $error->getPropertyPath(),
                    $error->getMessage()
                );
            }
            throw new \RuntimeException("validation failed:\n" . implode("\n", $errorMessages));
        }
    }

    /**
     * Process the payment using the specified payment method and payload.
     *
     * @throws \RuntimeException
     */
    private function processPayment(SymfonyStyle $io, PaymentMethodEnum $method, PaymentDto $paymentDto): int
    {
        try {
            $paymentDto->setMethod($method);

            // 1. Initialize the payment class and try to pay
            $paymentResponse = $this->paymentFactory
                ->get($method)
                ->init($paymentDto)
                ->pay();

            // 2. Check the payment response status
            if (!$paymentResponse->getResponse()->getStatus()) {
                $io->error('Payment failed!');
                return Command::FAILURE;
            }

            // 3. Normalize the payment response and display it
            $data = $this->serializer->normalize($paymentResponse, null, ['groups' => ['read']]);
            $io->success('Payment processed successfully');
            $io->writeln(json_encode($data, JSON_PRETTY_PRINT));

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            throw new \RuntimeException('Payment processing failed: ' . $e->getMessage());
        }
    }

}