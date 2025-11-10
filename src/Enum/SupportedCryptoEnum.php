<?php

namespace GryfOSS\CryptocurrenciesFormValidator\Enum;

use GryfOSS\CryptocurrenciesFormValidator\Address\EthereumAddress;
use GryfOSS\CryptocurrenciesFormValidator\Address\TronAddress;
use GryfOSS\CryptocurrenciesFormValidator\Transaction\EthereumTransactionValidator;
use GryfOSS\CryptocurrenciesFormValidator\Transaction\TronTransactionValidator;

/**
 * Enumeration of supported cryptocurrencies.
 *
 * This enum defines the cryptocurrencies supported by the validator,
 * providing methods to get the corresponding address validator classes,
 * transaction validator classes, and configuration keys for each cryptocurrency.
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Enum
 */
enum SupportedCryptoEnum: int
{
    /** @var int Ethereum cryptocurrency identifier */
    case ETHEREUM = 1;

    /** @var int Tron cryptocurrency identifier */
    case TRON = 2;

    /**
     * Get the address validator class for this cryptocurrency.
     *
     * @return string The fully qualified class name of the address validator
     */
    public function getAddressClass(): string
    {
        return match($this) {
            SupportedCryptoEnum::ETHEREUM => EthereumAddress::class,
            SupportedCryptoEnum::TRON => TronAddress::class,
        };
    }

    /**
     * Get the transaction validator class for this cryptocurrency.
     *
     * @return string The fully qualified class name of the transaction validator
     */
    /**
     * Get the transaction validator class for this cryptocurrency.
     *
     * @return string The fully qualified class name of the transaction validator
     */
    public function getTransactionValidatorClass(): string
    {
        return match($this) {
            SupportedCryptoEnum::ETHEREUM => EthereumTransactionValidator::class,
            SupportedCryptoEnum::TRON => TronTransactionValidator::class,
        };
    }

    /**
     * Get the configuration key for this cryptocurrency.
     *
     * @return string The configuration key used in settings and configuration files
     */
    public function getConfigKey(): string
    {
        return match($this) {
            SupportedCryptoEnum::ETHEREUM => 'eth',
            SupportedCryptoEnum::TRON => 'trx',
        };
    }
}
