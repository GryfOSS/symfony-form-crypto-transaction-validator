<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Tests\Unit\Transaction;

use GryfOSS\CryptocurrenciesFormValidator\Transaction\TronTransactionValidator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for TronTransactionValidator.
 */
class TronTransactionValidatorTest extends TestCase
{
    private TronTransactionValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new TronTransactionValidator('https://api.trongrid.io');
    }

    public function testConstructor(): void
    {
        $validator = new TronTransactionValidator('https://test-api.tron.network');
        $this->assertInstanceOf(TronTransactionValidator::class, $validator);
    }

    public function testConstructorWithDefaultHost(): void
    {
        $validator = new TronTransactionValidator();
        $this->assertInstanceOf(TronTransactionValidator::class, $validator);
    }

    public function testValidateWithDummyHash(): void
    {
        // Test with a dummy hash that won't exist on the blockchain
        $dummyHash = 'abc123def456789abc123def456789abc123def456789abc123def456789abc123';

        // We expect false here because it's a dummy hash that won't exist on the blockchain
        // and the API call will fail
        $result = $this->validator->isValid($dummyHash);
        $this->assertFalse($result);
    }

    public function testValidateWithEmptyHash(): void
    {
        $result = $this->validator->isValid('');
        $this->assertFalse($result);
    }

    public function testValidateWithInvalidHash(): void
    {
        $invalidHash = 'invalid-hash';
        $result = $this->validator->isValid($invalidHash);
        $this->assertFalse($result);
    }
}