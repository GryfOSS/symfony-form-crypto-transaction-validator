<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Tests\Unit\Enum;

use GryfOSS\CryptocurrenciesFormValidator\Address\EthereumAddress;
use GryfOSS\CryptocurrenciesFormValidator\Address\TronAddress;
use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;
use GryfOSS\CryptocurrenciesFormValidator\Transaction\EthereumTransactionValidator;
use GryfOSS\CryptocurrenciesFormValidator\Transaction\TronTransactionValidator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for SupportedCryptoEnum.
 */
class SupportedCryptoEnumTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals(1, SupportedCryptoEnum::ETHEREUM->value);
        $this->assertEquals(2, SupportedCryptoEnum::TRON->value);
    }

    /**
     * @dataProvider cryptoEnumProvider
     */
    public function testGetAddressClass(SupportedCryptoEnum $crypto, string $expectedClass): void
    {
        $this->assertEquals($expectedClass, $crypto->getAddressClass());
    }

    /**
     * @dataProvider cryptoEnumProvider
     */
    public function testGetTransactionValidatorClass(SupportedCryptoEnum $crypto, string $addressClass, string $expectedValidatorClass): void
    {
        $this->assertEquals($expectedValidatorClass, $crypto->getTransactionValidatorClass());
    }

    /**
     * @dataProvider cryptoConfigProvider
     */
    public function testGetConfigKey(SupportedCryptoEnum $crypto, string $expectedConfigKey): void
    {
        $this->assertEquals($expectedConfigKey, $crypto->getConfigKey());
    }

    public function cryptoEnumProvider(): array
    {
        return [
            [SupportedCryptoEnum::ETHEREUM, EthereumAddress::class, EthereumTransactionValidator::class],
            [SupportedCryptoEnum::TRON, TronAddress::class, TronTransactionValidator::class],
        ];
    }

    public function cryptoConfigProvider(): array
    {
        return [
            [SupportedCryptoEnum::ETHEREUM, 'eth'],
            [SupportedCryptoEnum::TRON, 'trx'],
        ];
    }

    public function testAllCases(): void
    {
        $cases = SupportedCryptoEnum::cases();
        $this->assertCount(2, $cases);
        $this->assertContains(SupportedCryptoEnum::ETHEREUM, $cases);
        $this->assertContains(SupportedCryptoEnum::TRON, $cases);
    }
}