<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Tests\Unit\Address;

use GryfOSS\CryptocurrenciesFormValidator\Address\EthereumAddress;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for EthereumAddress class.
 */
class EthereumAddressTest extends TestCase
{
    private EthereumAddress $validator;

    protected function setUp(): void
    {
        $this->validator = new EthereumAddress();
    }

    /**
     * @dataProvider validEthereumAddressesProvider
     */
    public function testValidEthereumAddresses(string $address): void
    {
        $this->assertTrue($this->validator->isValid($address));
    }

    /**
     * @dataProvider invalidEthereumAddressesProvider
     */
    public function testInvalidEthereumAddresses(string $address): void
    {
        $this->assertFalse($this->validator->isValid($address));
    }

    public function validEthereumAddressesProvider(): array
    {
        return [
            // All lowercase (valid)
            ['0x5aae5775959fbc2557cc8789bc1bf90a239d9c91'],

            // All uppercase (valid)
            ['0x5AAE5775959FBC2557CC8789BC1BF90A239D9C91'],

            // Mixed case with valid checksum
            ['0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed'],
            ['0xfB6916095ca1df60bB79Ce92cE3Ea74c37c5d359'],
            ['0xdbF03B407c01E7cD3CBea99509d93f8DDDC8C6FB'],
            ['0xD1220A0cf47c7B9Be7A2E6BA89F429762e7b9aDb'],

            // Without 0x prefix
            ['5aae5775959fbc2557cc8789bc1bf90a239d9c91'],
            ['5AAE5775959FBC2557CC8789BC1BF90A239D9C91'],
        ];
    }

    public function invalidEthereumAddressesProvider(): array
    {
        return [
            // Too short
            ['0x5aae5775959fbc2557cc8789bc1bf90a239d9c9'],

            // Too long
            ['0x5aae5775959fbc2557cc8789bc1bf90a239d9c911'],

            // Invalid characters
            ['0x5aae5775959fbc2557cc8789bc1bf90a239d9cgz'],

            // Mixed case with invalid checksum
            ['0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAeD'],
            ['0xfB6916095ca1df60bB79Ce92cE3Ea74c37c5d35A'],

            // Empty string
            [''],

            // Invalid prefix
            ['1x5aae5775959fbc2557cc8789bc1bf90a239d9c91'],
        ];
    }

    public function testMatchesPattern(): void
    {
        $reflection = new \ReflectionClass($this->validator);
        $method = $reflection->getMethod('matchesPattern');

        $this->assertEquals(1, $method->invoke($this->validator, '0x5aae5775959fbc2557cc8789bc1bf90a239d9c91'));
        $this->assertEquals(1, $method->invoke($this->validator, '5aae5775959fbc2557cc8789bc1bf90a239d9c91'));
        $this->assertEquals(0, $method->invoke($this->validator, '5aae5775959fbc2557cc8789bc1bf90a239d9c9'));
    }

    public function testIsAllSameCaps(): void
    {
        $reflection = new \ReflectionClass($this->validator);
        $method = $reflection->getMethod('isAllSameCaps');

        $this->assertTrue($method->invoke($this->validator, '0x5aae5775959fbc2557cc8789bc1bf90a239d9c91'));
        $this->assertTrue($method->invoke($this->validator, '0x5AAE5775959FBC2557CC8789BC1BF90A239D9C91'));
        $this->assertFalse($method->invoke($this->validator, '0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed'));
    }

    public function testIsValidChecksum(): void
    {
        $reflection = new \ReflectionClass($this->validator);
        $method = $reflection->getMethod('isValidChecksum');

        // Valid checksum
        $this->assertTrue($method->invoke($this->validator, '0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed'));
        $this->assertTrue($method->invoke($this->validator, '0xfB6916095ca1df60bB79Ce92cE3Ea74c37c5d359'));

        // Invalid checksum
        $this->assertFalse($method->invoke($this->validator, '0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAeD'));
    }
}