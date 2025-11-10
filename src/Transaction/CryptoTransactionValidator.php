<?php

declare(strict_types=1);

namespace GryfOSS\CryptocurrenciesFormValidator\Transaction;

/**
 * Interface for cryptocurrency transaction validation.
 *
 * This interface defines the contract for validating cryptocurrency transactions
 * across different blockchain networks. Implementations should verify that a
 * transaction hash exists and is valid on the respective blockchain.
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Transaction
 */
interface CryptoTransactionValidator
{
    /**
     * Validates a cryptocurrency transaction hash.
     *
     * @param string $transactionHash The transaction hash to validate
     * @return bool True if the transaction exists and is valid, false otherwise
     */
    public function isValid(string $transactionHash): bool;
}

