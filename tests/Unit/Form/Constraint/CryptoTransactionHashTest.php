<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Tests\Unit\Form\Constraint;

use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoTransactionHash;
use GryfOSS\CryptocurrenciesFormValidator\Form\Validator\CryptoTransactionHashValidator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for CryptoTransactionHash constraint.
 */
class CryptoTransactionHashTest extends TestCase
{
    public function testDefaultMessage(): void
    {
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM);
        $this->assertEquals('This value is not a valid transaction hash.', $constraint->message);
    }

    public function testCustomMessage(): void
    {
        $customMessage = 'Invalid transaction hash provided';
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM, $customMessage);
        $this->assertEquals($customMessage, $constraint->message);
    }

    public function testCryptoCurrency(): void
    {
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::TRON);
        $this->assertEquals(SupportedCryptoEnum::TRON, $constraint->cryptoCurrency);
    }

    public function testValidatedBy(): void
    {
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM);
        $this->assertEquals(CryptoTransactionHashValidator::class, $constraint->validatedBy());
    }

    public function testWithGroups(): void
    {
        $groups = ['group1', 'group2'];
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM, null, $groups);
        $this->assertEquals($groups, $constraint->groups);
    }

    public function testEthereumConstraint(): void
    {
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::ETHEREUM);
        $this->assertEquals(SupportedCryptoEnum::ETHEREUM, $constraint->cryptoCurrency);
        $this->assertEquals(CryptoTransactionHashValidator::class, $constraint->validatedBy());
    }

    public function testTronConstraint(): void
    {
        $constraint = new CryptoTransactionHash(SupportedCryptoEnum::TRON);
        $this->assertEquals(SupportedCryptoEnum::TRON, $constraint->cryptoCurrency);
        $this->assertEquals(CryptoTransactionHashValidator::class, $constraint->validatedBy());
    }
}