<?php

namespace GryfOSS\CryptocurrenciesFormValidator\Address;

use IEXBase\TronAPI\Support\Base58Check;
use IEXBase\TronAPI\Support\Hash;

/**
 * Tron address validator.
 *
 * This class implements validation for Tron (TRX) addresses using Base58Check
 * encoding and SHA256 double hashing for checksum validation. Tron addresses
 * are 34 characters long and start with the prefix "41".
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Address
 */
class TronAddress implements CryptoAddressInterface
{
    /** @var int The expected length of a Tron address */
    public const ADDRESS_SIZE = 34;

    /** @var string The hex prefix for Tron addresses */
    public const ADDRESS_PREFIX = "41";

    /** @var int The byte value of the Tron address prefix */
    public const ADDRESS_PREFIX_BYTE = 0x41;

    /**
     * Validates a Tron address.
     *
     * Performs comprehensive validation including:
     * - Length validation (must be 34 characters)
     * - Base58Check decoding
     * - Prefix validation (must start with 0x41)
     * - SHA256 double hash checksum validation
     *
     * @param string $address The Tron address to validate
     * @return bool True if the address is valid, false otherwise
     */
    public function isValid(string $address): bool
    {
        if (strlen($address) !== self::ADDRESS_SIZE) {
            return false;
        }

        $address = Base58Check::decode($address, 0, 0, false);
        $utf8 = hex2bin($address);

        if (strlen($utf8) !== 25) {
            return false;
        }
        if (strpos($utf8, chr(self::ADDRESS_PREFIX_BYTE)) !== 0) {
            return false;
        }

        $checkSum = substr($utf8, 21);
        $address = substr($utf8, 0, 21);

        $hash0 = Hash::SHA256($address);
        $hash1 = Hash::SHA256($hash0);
        $checkSum1 = substr($hash1, 0, 4);

        if ($checkSum === $checkSum1) {
            return true;
        }
        return false;
    }

}
