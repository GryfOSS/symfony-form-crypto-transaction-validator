<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private ?TestKernel $kernel = null;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        // Load environment variables
        if (file_exists(__DIR__ . '/../../.env')) {
            $dotenv = new Dotenv();
            $dotenv->load(__DIR__ . '/../../.env');
        }

        require_once __DIR__ . '/../../vendor/autoload.php';
        require_once __DIR__ . '/../../TestKernel.php';
        require_once __DIR__ . '/../../TestController.php';
    }

    /**
     * @Given I have a Symfony application with crypto validators
     */
    public function iHaveASymfonyApplicationWithCryptoValidators()
    {
        $this->kernel = new TestKernel('test', true);
        $this->kernel->boot();
    }

    /**
     * @Given I have configured Etherscan API access
     */
    public function iHaveConfiguredEtherscanApiAccess()
    {
        $apiKey = $_ENV['ETHERSCAN_API_KEY'] ?? null;
        if (!$apiKey) {
            throw new \RuntimeException('ETHERSCAN_API_KEY environment variable is not set');
        }

        // Verify API key format (should be a hex string)
        if (!ctype_alnum($apiKey) || strlen($apiKey) !== 34) {
            throw new \RuntimeException('Invalid ETHERSCAN_API_KEY format');
        }
    }

    /**
     * Get the Symfony kernel for testing
     */
    public function getKernel(): TestKernel
    {
        if (!$this->kernel) {
            throw new \RuntimeException('Kernel not initialized. Call "I have a Symfony application with crypto validators" first.');
        }

        return $this->kernel;
    }

    /**
     * Get environment variable
     */
    public function getEnv(string $key, ?string $default = null): ?string
    {
        return $_ENV[$key] ?? $default;
    }
}