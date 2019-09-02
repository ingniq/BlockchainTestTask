<?php

namespace Ingniq\BlockchainTestTask;

use Exception;
use SplDoublyLinkedList;

/**
 * Class BlockChain.
 * The longest chain of Block objects within a Block Tree.
 * @package Ingniq\BlockchainTestTask
 */
class BlockChain
{
    /**
     * A tree of Block objects.
     *
     * @var BlockTree
     */
    private $blockTree;

    /**
     * BlockChain constructor.
     *
     * Parameter initialization.
     */
    public function __construct()
    {
        $this->blockTree = new BlockTree();
    }

    /**
     * The method returns a list of Blocks within the longest chain of Blocks in the Block Tree.
     *
     * @return SplDoublyLinkedList
     * @throws Exception
     */
    public function getBlockChain()
    {
        return $this->blockTree->getLongestChain();
    }

    /**
     * Validation block
     *
     * @param Block $block
     * @return boolean
     */
    public function validateBlock(Block $block)
    {
        $transactions = $block->getTransactions();
        //the block has at least 1 transaction
        if (empty($transactions)) {
            return false;
        }
        //the block with the same id doesn’t exist in the “Block Tree” yet
        if ($this->blockTree->getNode($block->getId())) {
            return false;
        }

        return true;
    }

    /**
     * Adding block to the “Block Tree”.
     *
     * @param int   $parentId
     * @param Block $block
     * @return void
     * @throws Exception
     */
    public function addBlock($parentId, Block $block)
    {
        //validate the block
        if (!$this->validateBlock($block)) {
            return;
        }
        //there can be only one root block
        if (is_null($parentId) && !is_null($this->blockTree->getRoot())) {
            return;
        }
        //parentBlockId refers to a block that doesn’t exist in the “Block Tree”
        if (!is_null($parentId) && !$this->blockTree->getNode($parentId)) {
            return;
        }
        //Adding a "block" to an existing "parent Block Id" should not result in a negative balance on some accounts.
        if (!$this->validateAmount($block)) {
            return;
        }

        $this->blockTree->addBlock($parentId, $block);
    }

    /**
     * Validate amount.
     *
     * @param Block $block
     * @return bool
     * @throws Exception
     */
    private function validateAmount(Block $block)
    {
        /** @var Transaction $transaction */
        foreach ($block->getTransactions() as $transaction) {
            //Only transactions of the 'transfer' type can result in a negative balance
            if ($transaction->getType() === Transaction::TYPE_TRANSFER) {
                $curBalance = $this->getBalance($transaction->getFrom());

                if (0 > ($curBalance - $transaction->getAmount())) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Return a calculated account balance, using the longest existing chain of blocks in the tree.
     *
     * @param string $accountName
     * @return int
     * @throws Exception
     */
    public function getBalance($accountName)
    {
        //if the “account” is null
        if (is_null($accountName)) {
            throw new Exception('The "account" property must not be null.');
        }
        //if the “account” is shorter than 2 characters or longer than 100 characters
        $length = strlen($accountName);
        if ($length < 2 || $length > 10) {
            throw new Exception('The length of the "account" property is not in a valid range.');
        }

        $accountName = strtolower($accountName);
        $account     = Account::getAccount($accountName);
        /** @var BlockTreeNode $node */
        foreach ($this->getBlockChain() as $node) {
            $transactions = $node->getBlock()->getTransactions();
            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                //Increases the balance of those accounts whose name is specified in the "to" parameter of the transaction.
                if ($transaction->getTo() === $accountName) {
                    $account['balance'] += $transaction->getAmount();
                }
                //Decreases the balance of those accounts whose name is specified in the "from" parameter of the transaction.
                if ($transaction->getFrom() === $accountName) {
                    $account['balance'] -= $transaction->getAmount();
                }
            }
        }

        return $account['balance'];
    }
}