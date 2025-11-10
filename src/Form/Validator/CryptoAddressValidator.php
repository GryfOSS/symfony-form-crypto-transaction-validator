<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Form\Validator;

use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoAddress;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validator for cryptocurrency addresses.
 *
 * This validator handles the CryptoAddress constraint by instantiating
 * the appropriate address validator class based on the cryptocurrency type
 * and validating the provided address value.
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Form\Validator
 */
class CryptoAddressValidator extends ConstraintValidator
{
    /**
     * Constructor for the address validator.
     *
     * Note: This validator doesn't require the factory as it directly
     * instantiates address validators, but it's kept for potential future use.
     */
    public function __construct()
    {
    }

    /**
     * Validates a cryptocurrency address value.
     *
     * @param mixed $value The value to validate
     * @param Constraint $constraint The constraint being validated against
     * @return void
     *
     * @throws UnexpectedTypeException If the constraint is not a CryptoAddress instance
     * @throws UnexpectedValueException If the value is not a string
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CryptoAddress) {
            throw new UnexpectedTypeException($constraint, CryptoAddress::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $addressClass = $constraint->cryptoCurrency->getAddressClass();
        $addressObject = new $addressClass();

        if (false === $addressObject->isValid($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
