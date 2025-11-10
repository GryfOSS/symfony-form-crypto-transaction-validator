<?php

namespace GryfOSS\CryptocurrenciesFormValidator\Transaction;

use IEXBase\TronAPI\Exception\TronException;
use IEXBase\TronAPI\Provider\HttpProvider;
use IEXBase\TronAPI\Tron;

/**
 * Tron transaction validator.
 *
 * This class validates Tron transaction hashes by connecting to the TronGrid API
 * and verifying the transaction exists on the Tron blockchain.
 *
 * @package GryfOSS\CryptocurrenciesFormValidator\Transaction
 */
class TronTransactionValidator implements CryptoTransactionValidator
{
    /**
     * Constructor for the Tron transaction validator.
     *
     * @param string $tronGridApiHost The TronGrid API host URL for blockchain queries
     */
    public function __construct(protected string $tronGridApiHost = 'https://api.trongrid.io')
    {

    }

    /**
     * Validates a Tron transaction hash.
     *
     * Creates connections to the TronGrid API and queries for the transaction
     * details to verify the transaction exists on the Tron blockchain.
     *
     * @param string $transactionHash The Tron transaction hash to validate
     * @return bool True if the transaction is valid and exists, false otherwise
     */
    public function isValid(string $transactionHash): bool
    {
        $fullNode = new HttpProvider($this->tronGridApiHost);
        $solidityNode = new HttpProvider($this->tronGridApiHost);
        $eventServer = new HttpProvider($this->tronGridApiHost);

        try {
            $tron = new Tron($fullNode, $solidityNode, $eventServer);
            $transactionDetails = $tron->getTransaction($transactionHash);

            $isValid = isset($transactionDetails['txID']) && $transactionDetails['txID'] === $transactionHash;

            return $isValid;
        } catch (TronException $e) {
            return false;
        }
    }
}

