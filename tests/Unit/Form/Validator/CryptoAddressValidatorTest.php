<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Tests\Unit\Form\Validator;

use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoAddress;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoTransactionHash;
use GryfOSS\CryptocurrenciesFormValidator\Form\Validator\CryptoAddressValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * Unit tests for CryptoAddressValidator.
 */
class CryptoAddressValidatorTest extends TestCase
{
    private CryptoAddressValidator $validator;
    private ExecutionContextInterface $context;
    private ConstraintViolationBuilderInterface $violationBuilder;

    protected function setUp(): void
    {
        $this->validator = new CryptoAddressValidator();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->validator->initialize($this->context);
    }

    public function testValidateWithWrongConstraintType(): void
    {
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM);

        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('some-address', $constraint);
    }

    public function testValidateWithNullValue(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::ETHEREUM);

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate(null, $constraint);
    }

    public function testValidateWithEmptyString(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::ETHEREUM);

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('', $constraint);
    }

    public function testValidateWithNonString(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::ETHEREUM);

        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(123, $constraint);
    }

    public function testValidateValidEthereumAddress(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::ETHEREUM);
        $validAddress = '0x5aae5775959fbc2557cc8789bc1bf90a239d9c91';

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($validAddress, $constraint);
    }

    public function testValidateInvalidEthereumAddress(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::ETHEREUM);
        $invalidAddress = 'invalid-address';

        $this->violationBuilder->expects($this->once())->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->violationBuilder);

        $this->validator->validate($invalidAddress, $constraint);
    }

    public function testValidateValidTronAddress(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::TRON);
        // Using a real valid Tron address
        $validAddress = 'TLyqzVGLV1srkB7dToTAEqgDSfPtXRJZYH';

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate($validAddress, $constraint);
    }

    public function testValidateInvalidTronAddress(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::TRON);
        $invalidAddress = 'invalid-tron-address';

        $this->violationBuilder->expects($this->once())->method('addViolation');

        $this->context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($this->violationBuilder);

        $this->validator->validate($invalidAddress, $constraint);
    }
}