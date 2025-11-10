<?php

namespace GryfOSS\CryptocurrenciesFormValidator\Form\Constraint;

use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;
use GryfOSS\CryptocurrenciesFormValidator\Form\Validator\CryptoAddressValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Constraint for validating cryptocurrency addresses.
 *
 * This constraint validates cryptocurrency addresses for specific blockchain networks.
 * It uses the corresponding address validator based on the specified cryptocurrency type.
 *
 * Example usage:
 * ```php
 * #[CryptoAddress(SupportedCryptoEnum::ETHEREUM)]
 * private string $ethereumAddress;
 *
 * #[CryptoAddress(SupportedCryptoEnum::TRON, message: 'Invalid Tron address')]
 * private string $tronAddress;
 * ```
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Form\Constraint
 */
#[\Attribute]
class CryptoAddress extends Constraint
{
    /** @var string The default validation error message */
    public string $message = 'This value is not a valid cryptocurrency address.';

    /**
     * Constructor for the CryptoAddress constraint.
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
        return CryptoAddressValidator::class;
    }
}
