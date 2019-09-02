<?php

namespace Ingniq\BlockchainTestTask;

/**
 * Class Block.
 * A list of transactions.
 * @package Ingniq\BlockchainTestTask
 */
class Block
{
    /**
     * Unique block id
     *
     * @var int
     */
    private $id;
    /**
     * A list of transactions within this block
     *
     * @var array
     */
    private $transactions;

    /**
     * Block constructor.
     *
     * Parameter initialization.
     */
    public function __construct()
    {
        $this->transactions = array();
    }

    /**
     * Get 'id' property.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set 'id' property.
     *
     * @param int $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get list of transactions.
     *
     * @return array
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Check if transaction signature is valid.
     *
     * @param Transaction $transaction
     * @return boolean
     */
    public function validateTransaction($transaction)
    {
        /**
         * In order to calculate the signature, this should convert all properties(id, type, from, to, amount) to strings,
         * and concatenate them using a semicolon (“:”) character.
         */
        $componentsSignature = array(
            $transaction->getId(),
            $transaction->getType(),
            $transaction->getFrom(),
            $transaction->getTo(),
            $transaction->getAmount(),
        );

        $signature = MD5(implode(':', $componentsSignature));
        if ($signature !== $transaction->getSignature()) {
            return false;
        }

        return true;
    }

    /**
     * Add transaction to a list of transactions.
     *
     * @param Transaction $transaction
     * @return void
     */
    public function addTransaction($transaction)
    {
        if (!$this->validateTransaction($transaction)) {
            return;
        }
        //check if the number of existing transactions in block is less than 10
        if (count($this->transactions) >= 10) {
            return;
        }
        //check if transaction with transaction.id doesn’t already exist in the list of transactions in this block
        if (isset($this->transactions[$transaction->getId()])) {
            return;
        }

        $this->transactions[$transaction->getId()] = $transaction;
    }
}