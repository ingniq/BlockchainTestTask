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
     * Maximum number of existing transactions in a block.
     */
    const TRANSACTION_LIMIT_IN_BLOCK = 10;

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
    public function validateTransaction(Transaction $transaction)
    {
        $componentsSignature = array(
            (string) $transaction->getId(),
            (string) $transaction->getType(),
            $transaction->getFrom(),
            $transaction->getTo(),
            (string) $transaction->getAmount(),
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
    public function addTransaction(Transaction $transaction)
    {
        if (!$this->validateTransaction($transaction)) {
            return;
        }

        if (count($this->transactions) >= self::TRANSACTION_LIMIT_IN_BLOCK) {
            return;
        }

        if (isset($this->transactions[$transaction->getId()])) {
            return;
        }

        $this->transactions[$transaction->getId()] = $transaction;
    }
}