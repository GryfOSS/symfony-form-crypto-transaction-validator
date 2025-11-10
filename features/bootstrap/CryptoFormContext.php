<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoAddress;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoTransactionHash;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Context for testing cryptocurrency form validation
 */
class CryptoFormContext extends FeatureContext implements Context
{
    private ?FormInterface $form = null;
    private array $formData = [];
    private ?array $validationResult = null;
    private string $currentFormType = 'address'; // 'address' or 'transaction'

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @Given I am on the address validation form
     */
    public function iAmOnTheAddressValidationForm()
    {
        $this->currentFormType = 'address';
        $this->createAddressForm();
    }

    /**
     * @Given I am on the transaction validation form
     */
    public function iAmOnTheTransactionValidationForm()
    {
        $this->currentFormType = 'transaction';
        $this->createTransactionForm();
    }

    /**
     * @When I select :cryptoType as the crypto type
     */
    public function iSelectAsTheCryptoType(string $cryptoType)
    {
        $this->formData['crypto_type'] = $cryptoType;
    }

    /**
     * @When I enter :address as the address
     */
    public function iEnterAsTheAddress(string $address)
    {
        $this->formData['address'] = $address;
    }

    /**
     * @When I enter :transactionHash as the transaction hash
     */
    public function iEnterAsTheTransactionHash(string $transactionHash)
    {
        $this->formData['transaction_hash'] = $transactionHash;
    }

    /**
     * @When I submit the form
     */
    public function iSubmitTheForm()
    {
        if (!$this->form) {
            throw new \RuntimeException('No form has been created');
        }

        // Add submit button data to ensure form is considered submitted
        $this->formData['submit'] = 'Submit';

        // Create a request with the form data
        $request = Request::create('/', 'POST', [$this->form->getName() => $this->formData]);
        $this->form->handleRequest($request);

        // Validate the form and capture results
        $this->validateCurrentForm();
    }

    /**
     * @Then the form should be valid
     */
    public function theFormShouldBeValid()
    {
        if (!$this->validationResult) {
            throw new \RuntimeException('No validation result available');
        }

        if ($this->validationResult['status'] !== 'success') {
            $errors = isset($this->validationResult['errors']) ?
                implode(', ', $this->validationResult['errors']) :
                'Unknown errors';
            throw new \RuntimeException(
                "Form validation failed. Status: {$this->validationResult['status']}, Errors: {$errors}"
            );
        }
    }

    /**
     * @Then the form should be invalid
     */
    public function theFormShouldBeInvalid()
    {
        if (!$this->validationResult) {
            throw new \RuntimeException('No validation result available');
        }

        if ($this->validationResult['status'] !== 'error') {
            throw new \RuntimeException(
                "Expected form to be invalid but got status: {$this->validationResult['status']}"
            );
        }
    }

    /**
     * @Then I should see :message
     */
    public function iShouldSee(string $message)
    {
        if (!$this->validationResult) {
            throw new \RuntimeException('No validation result available');
        }

        $actualMessage = $this->validationResult['message'] ?? '';
        if (strpos($actualMessage, $message) === false) {
            // Also check in errors
            $foundInErrors = false;
            if (isset($this->validationResult['errors'])) {
                foreach ($this->validationResult['errors'] as $error) {
                    if (strpos($error, $message) !== false) {
                        $foundInErrors = true;
                        break;
                    }
                }
            }

            if (!$foundInErrors) {
                throw new \RuntimeException(
                    "Expected to see '{$message}' but got: {$actualMessage}. Errors: " .
                    json_encode($this->validationResult['errors'] ?? [])
                );
            }
        }
    }

    /**
     * @Then the transaction should exist on the Tron network
     */
    public function theTransactionShouldExistOnTheTronNetwork()
    {
        $transactionHash = $this->formData['transaction_hash'] ?? null;
        if (!$transactionHash) {
            throw new \RuntimeException('No transaction hash to verify');
        }

        // For Tron, we'll make a simple API call to verify the transaction exists
        // This is a basic check - in production you'd use a proper Tron API client
        $tronApiUrl = "https://api.trongrid.io/v1/transactions/{$transactionHash}";

        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET',
                'header' => 'Content-Type: application/json'
            ]
        ]);

        $response = @file_get_contents($tronApiUrl, false, $context);
        if ($response === false) {
            throw new \RuntimeException('Failed to connect to Tron API');
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['txID'])) {
            throw new \RuntimeException("Transaction {$transactionHash} not found on Tron network");
        }
    }

    /**
     * @Then the transaction should exist on the Ethereum network
     */
    public function theTransactionShouldExistOnTheEthereumNetwork()
    {
        $transactionHash = $this->formData['transaction_hash'] ?? null;
        if (!$transactionHash) {
            throw new \RuntimeException('No transaction hash to verify');
        }

        $apiKey = $this->getEnv('ETHERSCAN_API_KEY');
        if (!$apiKey) {
            throw new \RuntimeException('ETHERSCAN_API_KEY not configured');
        }

        // Use Etherscan API to verify the transaction
        $etherscanUrl = "https://api.etherscan.io/api?module=transaction&action=gettxreceiptstatus&txhash={$transactionHash}&apikey={$apiKey}";

        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET'
            ]
        ]);

        $response = @file_get_contents($etherscanUrl, false, $context);
        if ($response === false) {
            throw new \RuntimeException('Failed to connect to Etherscan API');
        }

        $data = json_decode($response, true);
        if (!$data || $data['status'] !== '1') {
            throw new \RuntimeException("Transaction {$transactionHash} not found on Ethereum network or API error");
        }
    }

    /**
     * Create address validation form
     */
    private function createAddressForm(): void
    {
        $kernel = $this->getKernel();
        $formFactory = $kernel->getContainer()->get('public.form.factory');

        $this->form = $formFactory->createBuilder()
            ->add('crypto_type', ChoiceType::class, [
                'choices' => [
                    'Tron' => 'tron',
                    'Ethereum' => 'ethereum',
                ],
                'required' => true,
            ])
            ->add('address', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    // We'll add the crypto-specific constraint dynamically after form submission
                ],
            ])
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    /**
     * Create transaction validation form
     */
    private function createTransactionForm(): void
    {
        $kernel = $this->getKernel();
        $formFactory = $kernel->getContainer()->get('public.form.factory');

        $this->form = $formFactory->createBuilder()
            ->add('crypto_type', ChoiceType::class, [
                'choices' => [
                    'Tron' => 'tron',
                    'Ethereum' => 'ethereum',
                ],
                'required' => true,
            ])
            ->add('transaction_hash', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    // We'll add the crypto-specific constraint dynamically after form submission
                ],
            ])
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    /**
     * Validate the current form and set results
     */
    private function validateCurrentForm(): void
    {
        if (!$this->form || !$this->form->isSubmitted()) {
            throw new \RuntimeException('Form not submitted');
        }

        // Now we need to add the crypto-specific constraints to the form based on the selected crypto type
        $this->addCryptoConstraintsToForm();

        if ($this->form->isValid()) {
            $data = $this->form->getData();
            $this->validationResult = [
                'status' => 'success',
                'message' => $this->currentFormType === 'address' ? 'Address is valid' : 'Transaction is valid',
                'data' => $data
            ];
        } else {
            $this->validationResult = [
                'status' => 'error',
                'message' => $this->currentFormType === 'address' ? 'Address validation failed' : 'Transaction validation failed',
                'errors' => []
            ];

            // Collect form errors
            foreach ($this->form->getErrors(true) as $error) {
                $this->validationResult['errors'][] = $error->getMessage();
            }
        }
    }

    /**
     * Add crypto-specific constraints to the form fields based on selected crypto type
     */
    private function addCryptoConstraintsToForm(): void
    {
        $data = $this->form->getData();
        $cryptoType = $data['crypto_type'] ?? null;

        if (!$cryptoType) {
            return;
        }

        $cryptoEnum = $cryptoType === 'tron' ? SupportedCryptoEnum::TRON : SupportedCryptoEnum::ETHEREUM;

        // Get the form configuration and rebuild it with proper constraints
        $kernel = $this->getKernel();
        $formFactory = $kernel->getContainer()->get('public.form.factory');

        if ($this->currentFormType === 'address') {
            // Rebuild form with crypto address constraint
            $this->form = $formFactory->createBuilder()
                ->add('crypto_type', ChoiceType::class, [
                    'choices' => [
                        'Tron' => 'tron',
                        'Ethereum' => 'ethereum',
                    ],
                    'required' => true,
                    'data' => $cryptoType
                ])
                ->add('address', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(),
                        new CryptoAddress($cryptoEnum)
                    ],
                    'data' => $data['address'] ?? ''
                ])
                ->add('submit', SubmitType::class)
                ->getForm();
        } else {
            // Rebuild form with crypto transaction hash constraint
            $this->form = $formFactory->createBuilder()
                ->add('crypto_type', ChoiceType::class, [
                    'choices' => [
                        'Tron' => 'tron',
                        'Ethereum' => 'ethereum',
                    ],
                    'required' => true,
                    'data' => $cryptoType
                ])
                ->add('transaction_hash', TextType::class, [
                    'required' => true,
                    'constraints' => [
                        new Assert\NotBlank(),
                        new CryptoTransactionHash($cryptoEnum)
                    ],
                    'data' => $data['transaction_hash'] ?? ''
                ])
                ->add('submit', SubmitType::class)
                ->getForm();
        }

        // Re-submit the form with the same data to trigger validation with the new constraints
        $request = Request::create('/', 'POST', [$this->form->getName() => $this->formData]);
        $this->form->handleRequest($request);
    }
}