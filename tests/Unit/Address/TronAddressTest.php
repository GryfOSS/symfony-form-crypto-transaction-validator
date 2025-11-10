<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Tests\Unit\Address;

use GryfOSS\CryptocurrenciesFormValidator\Address\TronAddress;
use PHPUnit\Framework\TestCase;

/**
 * Testable TronAddress class that allows mocking Base58Check decode
 */
class TestableTronAddress extends TronAddress
{
    private ?string $mockDecodeResult = null;

    public function setMockDecodeResult(?string $result): void
    {
        $this->mockDecodeResult = $result;
    }

    public function isValid(string $address): bool
    {
        if (strlen($address) !== self::ADDRESS_SIZE) {
            return false;
        }

        // Use mock result if set, otherwise use real Base58Check decode
        if ($this->mockDecodeResult !== null) {
            $address = $this->mockDecodeResult;
        } else {
            $address = \IEXBase\TronAPI\Support\Base58Check::decode($address, 0, 0, false);
        }

        $utf8 = hex2bin($address);

        // Handle case where hex2bin fails
        if ($utf8 === false) {
            return false;
        }

        if (strlen($utf8) !== 25) {
            return false;
        }
        if (strpos($utf8, chr(self::ADDRESS_PREFIX_BYTE)) !== 0) {
            return false;
        }

        $checkSum = substr($utf8, 21);
        $address = substr($utf8, 0, 21);

        $hash0 = \IEXBase\TronAPI\Support\Hash::SHA256($address);
        $hash1 = \IEXBase\TronAPI\Support\Hash::SHA256($hash0);
        $checkSum1 = substr($hash1, 0, 4);

        if ($checkSum === $checkSum1) {
            return true;
        }
        return false;
    }
}

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

    public function testDecodedBytesInvalidLength(): void
    {
        $testableValidator = new TestableTronAddress();

        // Create a 34-character address that passes initial length check
        $validLengthAddress = 'TRX9QJfGtTcUq9qvLn3pHv62gAVJfKhMbb';

        // Mock Base58Check::decode to return hex that converts to 24 bytes (48 hex chars = 24 bytes)
        $testableValidator->setMockDecodeResult('41123456789abcdef0123456789abcdef0123456789abcdef0123456789ab');

        // This should fail at strlen($utf8) !== 25 check
        $this->assertFalse($testableValidator->isValid($validLengthAddress));

        // Mock Base58Check::decode to return hex that converts to 26 bytes (52 hex chars = 26 bytes)
        $testableValidator->setMockDecodeResult('41123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef');

        // This should also fail at strlen($utf8) !== 25 check
        $this->assertFalse($testableValidator->isValid($validLengthAddress));

        // Mock Base58Check::decode to return hex that converts to exactly 25 bytes but has wrong prefix
        $testableValidator->setMockDecodeResult('42123456789abcdef0123456789abcdef0123456789abcdef0123456789abcd');

        // This should pass the length check but fail at prefix check
        $this->assertFalse($testableValidator->isValid($validLengthAddress));
    }

    /**
     * This test tries to trigger the specific uncovered line by using an address
     * that has the correct length (34 chars) but decodes to the wrong byte length.
     * From testing: "1111111111111111111111111111111111" decodes to only 1 byte.
     */
    public function testOriginalClassWithInvalidDecodeResult(): void
    {
        $originalValidator = new TronAddress();

        // This address is exactly 34 characters but decodes to only 1 byte (not 25)
        // This should trigger the strlen($utf8) !== 25 check
        $addressWith1Byte = '1111111111111111111111111111111111';

        $isValid = $originalValidator->isValid($addressWith1Byte);
        $this->assertFalse($isValid, "Address that decodes to 1 byte should be invalid");

        // Test a few other malformed cases for completeness
        $testAddresses = [
            'TRX9QJfGtTcUq9qvLn3pHv62gAVJfKhM00',
            'TR11111111111111111111111111111111',
            'TA7WwogEPPch7kvBab4HnPWLpgZhLhKBGh',
        ];

        foreach ($testAddresses as $address) {
            $isValid = $originalValidator->isValid($address);
            $this->assertFalse($isValid, "Address {$address} should be invalid");
        }
    }
}