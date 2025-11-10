<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Tests\Unit\Form\Constraint;

use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;
use GryfOSS\CryptocurrenciesFormValidator\Form\Constraint\CryptoAddress;
use GryfOSS\CryptocurrenciesFormValidator\Form\Validator\CryptoAddressValidator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for CryptoAddress constraint.
 */
class CryptoAddressTest extends TestCase
{
    public function testDefaultMessage(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::ETHEREUM);
        $this->assertEquals('This value is not a valid cryptocurrency address.', $constraint->message);
    }

    public function testCustomMessage(): void
    {
        $customMessage = 'Invalid Ethereum address provided';
        $constraint = new CryptoAddress(SupportedCryptoEnum::ETHEREUM, $customMessage);
        $this->assertEquals($customMessage, $constraint->message);
    }

    public function testCryptoCurrency(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::TRON);
        $this->assertEquals(SupportedCryptoEnum::TRON, $constraint->cryptoCurrency);
    }

    public function testValidatedBy(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::ETHEREUM);
        $this->assertEquals(CryptoAddressValidator::class, $constraint->validatedBy());
    }

    public function testWithGroups(): void
    {
        $groups = ['group1', 'group2'];
        $constraint = new CryptoAddress(SupportedCryptoEnum::ETHEREUM, null, $groups);
        $this->assertEquals($groups, $constraint->groups);
    }

    public function testEthereumConstraint(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::ETHEREUM);
        $this->assertEquals(SupportedCryptoEnum::ETHEREUM, $constraint->cryptoCurrency);
        $this->assertEquals(CryptoAddressValidator::class, $constraint->validatedBy());
    }

    public function testTronConstraint(): void
    {
        $constraint = new CryptoAddress(SupportedCryptoEnum::TRON);
        $this->assertEquals(SupportedCryptoEnum::TRON, $constraint->cryptoCurrency);
        $this->assertEquals(CryptoAddressValidator::class, $constraint->validatedBy());
    }
}