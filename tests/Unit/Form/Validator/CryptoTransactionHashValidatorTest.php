<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Tests\Unit\Form\Validator;

use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;
use GryfOSS\CryptocurrenciesFormValidator\Factory\CryptoTransactionValidatorFactory;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoAddress;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoTransactionHash;
use GryfOSS\CryptocurrenciesFormValidator\Form\Validator\CryptoTransactionHashValidator;
use GryfOSS\CryptocurrenciesFormValidator\Transaction\CryptoTransactionValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * Unit tests for CryptoTransactionHashValidator.
 */
class CryptoTransactionHashValidatorTest extends TestCase
{
    private CryptoTransactionHashValidator $validator;
    private CryptoTransactionValidatorFactory $factory;
    private ExecutionContextInterface $context;
    private ConstraintViolationBuilderInterface $violationBuilder;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(CryptoTransactionValidatorFactory::class);
        $this->validator = new CryptoTransactionHashValidator($this->factory);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->validator->initialize($this->context);
    }

    public function testValidateWithWrongConstraintType(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::ETHEREUM);

        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('some-hash', $constraint);
    }

    public function testValidateWithNullValue(): void
    {
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM);

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate(null, $constraint);
    }

    public function testValidateWithEmptyString(): void
    {
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM);

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('', $constraint);
    }

    public function testValidateWithNonString(): void
    {
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM);

        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(123, $constraint);
    }

    public function testValidateValidTransaction(): void
    {
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM);
        $validHash = '0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef';

        $mockValidator = $this->createMock(CryptoTransactionValidator::class);
        $mockValidator->expects($this->once())
            ->method('isValid')
            ->with($validHash)
            ->willReturn(true);

        $this->factory->expects($this->once())
            ->method('createValidator')
            ->with(SupportedCryptoEnum::ETHEREUM)
            ->willReturn($mockValidator);

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($validHash, $constraint);
    }

    public function testValidateInvalidTransaction(): void
    {
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM);
        $invalidHash = 'invalid-hash';

        $mockValidator = $this->createMock(CryptoTransactionValidator::class);
        $mockValidator->expects($this->once())
            ->method('isValid')
            ->with($invalidHash)
            ->willReturn(false);

        $this->factory->expects($this->once())
            ->method('createValidator')
            ->with(SupportedCryptoEnum::ETHEREUM)
            ->willReturn($mockValidator);

        $this->violationBuilder->expects($this->once())->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->violationBuilder);

        $this->validator->validate($invalidHash, $constraint);
    }

    public function testValidateWithTronTransaction(): void
    {
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::TRON);
        $tronHash = 'abc123def456789abc123def456789abc123def456789abc123def456789abc123';

        $mockValidator = $this->createMock(CryptoTransactionValidator::class);
        $mockValidator->expects($this->once())
            ->method('isValid')
            ->with($tronHash)
            ->willReturn(true);

        $this->factory->expects($this->once())
            ->method('createValidator')
            ->with(SupportedCryptoEnum::TRON)
            ->willReturn($mockValidator);

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($tronHash, $constraint);
    }
}