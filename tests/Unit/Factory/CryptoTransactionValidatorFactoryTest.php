<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Tests\Unit\Factory;

use GryfOSS\CryptocurrenciesFormValidator\Enum\SupportedCryptoEnum;
use GryfOSS\CryptocurrenciesFormValidator\Factory\CryptoTransactionValidatorFactory;
use GryfOSS\CryptocurrenciesFormValidator\Transaction\EthereumTransactionValidator;
use GryfOSS\CryptocurrenciesFormValidator\Transaction\TronTransactionValidator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for CryptoTransactionValidatorFactory.
 */
class CryptoTransactionValidatorFactoryTest extends TestCase
{
    private CryptoTransactionValidatorFactory $factory;

    protected function setUp(): void
    {
        $config = [
            'eth' => 'dummy-etherscan-api-key',
            'trx' => 'https://api.trongrid.io',
        ];
        $this->factory = new CryptoTransactionValidatorFactory($config);
    }

    public function testCreateEthereumValidator(): void
    {
        $validator = $this->factory->createValidator(SupportedCryptoEnum::ETHEREUM);
        $this->assertInstanceOf(EthereumTransactionValidator::class, $validator);
    }

    public function testCreateTronValidator(): void
    {
        $validator = $this->factory->createValidator(SupportedCryptoEnum::TRON);
        $this->assertInstanceOf(TronTransactionValidator::class, $validator);
    }

    public function testCreateValidatorWithMissingConfig(): void
    {
        $emptyFactory = new CryptoTransactionValidatorFactory([]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration value for key `eth` is missing.');

        $emptyFactory->createValidator(SupportedCryptoEnum::ETHEREUM);
    }

    public function testCreateValidatorWithPartialConfig(): void
    {
        $partialConfig = ['eth' => 'api-key'];
        $partialFactory = new CryptoTransactionValidatorFactory($partialConfig);

        // This should work
        $validator = $partialFactory->createValidator(SupportedCryptoEnum::ETHEREUM);
        $this->assertInstanceOf(EthereumTransactionValidator::class, $validator);

        // This should fail
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration value for key `trx` is missing.');

        $partialFactory->createValidator(SupportedCryptoEnum::TRON);
    }

    public function testFactoryWithEmptyConstructor(): void
    {
        $emptyFactory = new CryptoTransactionValidatorFactory();

        $this->expectException(\InvalidArgumentException::class);
        $emptyFactory->createValidator(SupportedCryptoEnum::ETHEREUM);
    }
}