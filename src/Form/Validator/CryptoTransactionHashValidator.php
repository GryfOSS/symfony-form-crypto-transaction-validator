<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Form\Validator;

use GryfOSS\CryptocurrenciesFormValidator\Factory\CryptoTransactionValidatorFactory;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoTransactionHash;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validator for cryptocurrency transaction hashes.
 *
 * This validator handles the CryptoTransactionHash constraint by using
 * the CryptoTransactionValidatorFactory to create the appropriate
 * transaction validator and validate the provided transaction hash.
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Form\Validator
 */
class CryptoTransactionHashValidator extends ConstraintValidator
{
    /**
     * Constructor for the transaction hash validator.
     *
     * @param CryptoTransactionValidatorFactory $cryptoTransactionValidatorFactory Factory for creating transaction validators
     */
    public function __construct(
        protected readonly CryptoTransactionValidatorFactory $cryptoTransactionValidatorFactory,
    ) {
    }

    /**
     * Validates a cryptocurrency transaction hash value.
     *
     * @param mixed $value The value to validate (transaction hash)
     * @param Constraint $constraint The constraint being validated against
     * @return void
     *
     * @throws UnexpectedTypeException If the constraint is not a CryptoTransactionHash instance
     * @throws UnexpectedValueException If the value is not a string
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CryptoTransactionHash) {
            throw new UnexpectedTypeException($constraint, CryptoTransactionHash::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $validator = $this->cryptoTransactionValidatorFactory->createValidator($constraint->cryptoCurrency);

        if (false === $validator->isValid($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
