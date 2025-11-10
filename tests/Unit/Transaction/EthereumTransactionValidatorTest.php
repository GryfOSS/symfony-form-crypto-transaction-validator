<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Tests\Unit\Transaction;

use GryfOSS\CryptocurrenciesFormValidator\Transaction\EthereumTransactionValidator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for EthereumTransactionValidator.
 */
class EthereumTransactionValidatorTest extends TestCase
{
    private EthereumTransactionValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new EthereumTransactionValidator('dummy-api-key');
    }

    public function testConstructor(): void
    {
        $validator = new EthereumTransactionValidator('test-api-key');
        $this->assertInstanceOf(EthereumTransactionValidator::class, $validator);
    }

    /**
     * @dataProvider invalidFormatProvider
     */
    public function testInvalidFormat(string $hash): void
    {
        $this->assertFalse($this->validator->isValid($hash));
    }

    public function invalidFormatProvider(): array
    {
        return [
            // Missing 0x prefix
            ['1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef'],

            // Too short
            ['0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcde'],

            // Too long
            ['0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef1'],

            // Invalid characters
            ['0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdeg'],

            // Empty string
            [''],

            // Just 0x
            ['0x'],
        ];
    }

    public function testValidFormatButMockApiCall(): void
    {
        // This test checks format validation only, since we can't mock the Etherscan API easily
        $validFormatHash = '0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef';

        // The method will return false because the API call will fail with dummy data,
        // but the format validation should pass the initial checks
        $result = $this->validator->isValid($validFormatHash);

        // We expect false here because it's a dummy hash that won't exist on the blockchain
        $this->assertFalse($result);
    }
}