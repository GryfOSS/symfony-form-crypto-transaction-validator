<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Tests\Unit\Address;

use GryfOSS\CryptocurrenciesFormValidator\Address\TronAddress;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for TronAddress class.
 */
class TronAddressTest extends TestCase
{
    private TronAddress $validator;

    protected function setUp(): void
    {
        $this->validator = new TronAddress();
    }

    /**
     * @dataProvider validTronAddressesProvider
     */
    public function testValidTronAddresses(string $address): void
    {
        $this->assertTrue($this->validator->isValid($address));
    }

    /**
     * @dataProvider invalidTronAddressesProvider
     */
    public function testInvalidTronAddresses(string $address): void
    {
        $this->assertFalse($this->validator->isValid($address));
    }

    public function validTronAddressesProvider(): array
    {
        return [
            // Real valid Tron addresses
            ['TLyqzVGLV1srkB7dToTAEqgDSfPtXRJZYH'], // Tron Foundation address
            ['TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t'], // USDT TRC20 contract
            ['TKkeiboTkxXKJpbmVFbv4a8ov5rAfRDMf9'], // Known valid address
        ];
    }

    public function invalidTronAddressesProvider(): array
    {
        return [
            // Too short
            ['TRX9QJfGtTcUq9qvLn3pHv62gAVJfKhMb'],

            // Too long
            ['TRX9QJfGtTcUq9qvLn3pHv62gAVJfKhMbbb'],

            // Invalid characters
            ['TRX9QJfGtTcUq9qvLn3pHv62gAVJfKhM00'],

            // Wrong prefix
            ['BRX9QJfGtTcUq9qvLn3pHv62gAVJfKhMbb'],

            // Empty string
            [''],

            // Invalid Base58 characters
            ['TRX9QJfGtTcUq9qvLn3pHv62gAVJfKhM0O'],
        ];
    }

    public function testAddressConstants(): void
    {
        $this->assertEquals(34, TronAddress::ADDRESS_SIZE);
        $this->assertEquals("41", TronAddress::ADDRESS_PREFIX);
        $this->assertEquals(0x41, TronAddress::ADDRESS_PREFIX_BYTE);
    }

    public function testInvalidLength(): void
    {
        // Test addresses that are too short or too long
        $shortAddress = 'TRX9QJfGtTcUq9qvLn3pHv62gAVJfKhM';
        $longAddress = 'TRX9QJfGtTcUq9qvLn3pHv62gAVJfKhMbbb';

        $this->assertFalse($this->validator->isValid($shortAddress));
        $this->assertFalse($this->validator->isValid($longAddress));
    }
}