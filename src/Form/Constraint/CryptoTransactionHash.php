<?php

namespace GryfOSS\CryptocurrenciesFormValidator\Form\Constraint;

use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;
use GryfOSS\CryptocurrenciesFormValidator\Form\Validator\CryptoTransactionHashValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Constraint for validating cryptocurrency transaction hashes.
 *
 * This constraint validates transaction hashes for specific blockchain networks.
 * It uses the corresponding transaction validator to check if the transaction
 * hash exists and is valid on the specified blockchain.
 *
 * Example usage:
 * ```php
 * #[CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM)]
 * private string $ethereumTxHash;
 *
 * #[CryptoTransactionHash(SupportedCryptoEnum::TRON, message: 'Invalid Tron transaction')]
 * private string $tronTxHash;
 * ```
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Form\Constraint
 */
#[\Attribute]
class CryptoTransactionHash extends Constraint
{
    /** @var string The default validation error message */
    public string $message = 'This value is not a valid transaction hash.';

    /**
     * Constructor for the CryptoTransactionHash constraint.
     *
     * @param SupportedCryptoEnum $cryptoCurrency The cryptocurrency type to validate for
     * @param string|null $message Custom error message (optional)
     * @param array|null $groups Validation groups (optional)
     */
    public function __construct(
        public readonly SupportedCryptoEnum $cryptoCurrency,
        ?string $message = null,
        ?array $groups = null,
    ) {
        parent::__construct(groups: $groups);

        $this->message = $message ?? $this->message;
    }

    /**
     * Returns the validator class that should handle this constraint.
     *
     * @return string The fully qualified class name of the validator
     */
    public function validatedBy(): string
    {
        return CryptoTransactionHashValidator::class;
    }
}
