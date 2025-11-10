<?php

namespace GryfOSS\CryptocurrenciesFormValidator\Address;

/**
 * Interface for cryptocurrency address validation.
 *
 * This interface defines the contract for validating cryptocurrency addresses
 * across different blockchain networks such as Ethereum, Tron, etc.
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Address
 */
interface CryptoAddressInterface
{
    /**
     * Validates a cryptocurrency address.
     *
     * @param string $address The cryptocurrency address to validate
     * @return bool True if the address is valid, false otherwise
     */
    public function isValid(string $address): bool;
}
