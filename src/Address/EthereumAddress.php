<?php

namespace GryfOSS\CryptocurrenciesFormValidator\Address;

use kornrunner\Keccak;

/**
 * Ethereum address validator.
 *
 * This class implements validation for Ethereum addresses according to the
 * EIP-55 checksum validation standard. It supports both checksummed and
 * non-checksummed addresses.
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Address
 * @see https://eips.ethereum.org/EIPS/eip-55
 */
class EthereumAddress implements CryptoAddressInterface
{
    /**
     * Validates an Ethereum address.
     *
     * Checks if the address matches the Ethereum format pattern and
     * validates the checksum according to EIP-55 if present.
     *
     * @param string $address The Ethereum address to validate
     * @return bool True if the address is valid, false otherwise
     * @see https://github.com/ethereum/web3.js/blob/7935e5f/lib/utils/utils.js#L415
     */
    public function isValid(string $address): bool
    {
        if ($this->matchesPattern($address)) {
            return $this->isAllSameCaps($address) ?: $this->isValidChecksum($address);
        }

        return false;
    }

    /**
     * Checks if the address matches the Ethereum address pattern.
     *
     * @param string $address The address to check
     * @return int Returns 1 if pattern matches, 0 otherwise
     */
    protected function matchesPattern(string $address): int
    {
        return preg_match('/^(0x)?[0-9a-f]{40}$/i', $address);
    }

    /**
     * Checks if the address is all lowercase or all uppercase.
     *
     * Addresses that are all the same case (all lowercase or all uppercase)
     * are considered valid without checksum validation.
     *
     * @param string $address The address to check
     * @return bool True if all characters are the same case, false otherwise
     */
    protected function isAllSameCaps(string $address): bool
    {
        return preg_match('/^(0x)?[0-9a-f]{40}$/', $address) || preg_match('/^(0x)?[0-9A-F]{40}$/', $address);
    }

    /**
     * Validates the EIP-55 checksum of an Ethereum address.
     *
     * The checksum validation uses Keccak-256 hash to determine if each
     * alphabetical character should be uppercase or lowercase based on
     * the corresponding hash character value.
     *
     * @param string $address The address to validate checksum for
     * @return bool True if checksum is valid, false otherwise
     * @see https://github.com/web3j/web3j/pull/134/files#diff-db8702981afff54d3de6a913f13b7be4R42
     */
    protected function isValidChecksum($address)
    {
        $address = str_replace('0x', '', $address);
        $hash = Keccak::hash(strtolower($address), 256);

        for ($i = 0; $i < 40; $i++) {
            if (ctype_alpha($address[$i])) {
                // Each uppercase letter should correlate with a first bit of 1 in the hash char with the same index,
                // and each lowercase letter with a 0 bit.
                $charInt = intval($hash[$i], 16);

                if ((ctype_upper($address[$i]) && $charInt <= 7) || (ctype_lower($address[$i]) && $charInt > 7)) {
                    return false;
                }
            }
        }

        return true;
    }
}
