<?php

use Ingniq\BlockchainTestTask\Block;
use Ingniq\BlockchainTestTask\BlockChain;
use Ingniq\BlockchainTestTask\Transaction;

require_once __DIR__ . '/../bootstrap.php';

try {
    // create 100 coins and transfer them to Bob
    $trx = new Transaction();
    $trx->setId(1);
    $trx->setType(Transaction::TYPE_EMISSION);
    $trx->setTo("bob");
    $trx->setAmount(100);

    $componentsSignature = array(
        $trx->getId(),
        $trx->getType(),
        $trx->getFrom(),
        $trx->getTo(),
        $trx->getAmount(),
    );

    $signature = MD5(implode(':', $componentsSignature));
    $trx->setSignature($signature);

    $block = new Block();
    $block->setId(1);
    $block->addTransaction($trx);

    $blockChain = new BlockChain();
    $blockChain->addBlock(null, $block);

    // Bob transfer 50 coins to Alice
    $trx = new Transaction();
    $trx->setId(2);
    $trx->setType(Transaction::TYPE_TRANSFER);
    $trx->setFrom("bob");
    $trx->setTo("alice");
    $trx->setAmount(50);

    $componentsSignature = array(
        $trx->getId(),
        $trx->getType(),
        $trx->getFrom(),
        $trx->getTo(),
        $trx->getAmount(),
    );

    $signature = MD5(implode(':', $componentsSignature));
    $trx->setSignature($signature);

    $block = new Block();
    $block->setId(2);
    $block->addTransaction($trx);

    $blockChain->addBlock(1, $block);

    echo 'Alice: ' . $blockChain->getBalance('alice') . "\n";
    echo 'Bob: ' . $blockChain->getBalance('bob') . "\n";

} catch (Exception $e) {
    echo $e->getMessage();
}
