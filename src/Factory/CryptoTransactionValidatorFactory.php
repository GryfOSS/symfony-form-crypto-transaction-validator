<?php

namespace GryfOSS\CryptocurrenciesFormValidator\Factory;

use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;
use GryfOSS\CryptocurrenciesFormValidator\Transaction\CryptoTransactionValidator;

/**
 * Factory class for creating cryptocurrency transaction validators.
 *
 * This factory creates instances of transaction validators based on the
 * specified cryptocurrency type and configuration. It uses the SupportedCryptoEnum
 * to determine which validator class to instantiate and passes the appropriate
 * configuration to the validator constructor.
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Factory
 */
class CryptoTransactionValidatorFactory
{
    /**
     * Constructor for the factory.
     *
     * @param array $config Configuration array with cryptocurrency-specific settings.
     *                     Expected structure: ['eth' => [...], 'trx' => [...], ...]
     */
    public function __construct(
        protected array $config = []
    ) {
    }

    /**
     * Creates a transaction validator for the specified cryptocurrency.
     *
     * @param SupportedCryptoEnum $cryptoCurrency The cryptocurrency to create a validator for
     * @return CryptoTransactionValidator The created transaction validator instance
     * @throws \InvalidArgumentException When configuration for the cryptocurrency is missing
     */
    public function createValidator(SupportedCryptoEnum $cryptoCurrency): CryptoTransactionValidator
    {
        $class = $cryptoCurrency->getTransactionValidatorClass();

        $configKey = $cryptoCurrency->getConfigKey();
        $configValue = $this->config[$configKey] ?? null;

        if (!$configValue) {
            throw new \InvalidArgumentException("Configuration value for key `{$configKey}` is missing.");
        }

        return new $class($configValue);
    }
}
