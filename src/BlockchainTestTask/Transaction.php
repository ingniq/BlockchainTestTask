<?php

namespace Ingniq\BlockchainTestTask;

use Exception;

/**
 * Class Transaction.
 * A single transaction, that creates (“emits”) some coins or moves them between accounts.
 * @package Ingniq\BlockchainTestTask
 */
class Transaction
{
    /**
     * Money emission. It means the system creates new “coins” and puts them to the destination account of the transaction (“to” property).
     *
     * @var int
     */
    const TYPE_EMISSION = 0;
    /**
     * Money transfer. It means that within this transaction one account transfers money to some other account.
     *
     * @var int
     */
    const TYPE_TRANSFER = 1;

    /**
     * Maximum number of characters in the account name.
     */
    const ACCOUNT_LENGTH_MIN = 2;

    /**
     * Minimum number of characters in the account name.
     */
    const ACCOUNT_LENGTH_MAX = 10;

    /**
     * Signature string pattern.
     */
    const SIGNATURE_PATTERN = '/^[a-f0-9]{32}$/';

    /**
     * Unique transaction id.
     *
     * @var int
     */
    private $id;
    /**
     * Transaction type - money emission or money transfer.
     *
     * @var int
     */
    private $type;
    /**
     * Source account name.
     *
     * @var string|null
     */
    private $from;
    /**
     * Destination account name.
     *
     * @var string
     */
    private $to;
    /**
     * Transaction amount.
     *
     * @var int
     */
    private $amount;
    /**
     * MD5 hex digest of the transaction fields.
     *
     * @var string
     */
    private $signature;
    /**
     * List of supported transaction types.
     *
     * @var array
     */
    private $types;

    /**
     * Transaction constructor.
     *
     * Parameter initialization.
     */
    public function __construct()
    {
        $this->types = array(self::TYPE_EMISSION, self::TYPE_TRANSFER);
    }

    /**
     * Get 'id' property.
     *
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * Set 'id' property.
     *
     * @param int $id
     * @return void
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * Get 'type' property.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set 'type' property.
     *
     * @param int $type
     * @return void
     * @throws Exception
     */
    public function setType(int $type)
    {
        if (!in_array($type, $this->types)) {
            throw new Exception('An unsupported transaction type.');
        }
        if ($type == self::TYPE_EMISSION) {
            $this->from = null;
        }

        $this->type = $type;
    }

    /**
     * Get 'from' property.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set 'from' property.
     *
     * @param string $from
     * @return void
     * @throws Exception
     */
    public function setFrom(string $from)
    {
        //if passed “from” account is  shorter than 2 characters or longer than 10 characters.
        $length = strlen($from);

        if ($length < self::ACCOUNT_LENGTH_MIN || $length > self::ACCOUNT_LENGTH_MAX) {
            throw new Exception('The length of the "from" property is not in a valid range.');
        }

        //if transaction “type” is “emission” – ignore the passed “from” value and set “from” property to null.
        if ($this->getType() == self::TYPE_EMISSION) {
            $this->from = null;
        }

        $this->from = strtolower($from);
    }

    /**
     * Get 'to' property.
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Set 'to' property.
     *
     * @param string $to
     * @return void
     * @throws Exception
     */
    public function setTo(string $to)
    {
        //if “to” account is the same as the “from” account
        if ($to == $this->from) {
            throw new Exception('The "from" and "to" properties must not be the same.');
        }

        //if passed “to” account is shorter than 2 characters or longer than 10 characters.
        $length = strlen($to);

        if ($length < self::ACCOUNT_LENGTH_MIN || $length > self::ACCOUNT_LENGTH_MAX) {
            throw new Exception('The length of the "to" property is not in a valid range.');
        }

        $this->to = strtolower($to);
    }

    /**
     * Get 'amount' property.
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set 'amount' property.
     *
     * @param int $amount
     * @return void
     * @throws Exception
     */
    public function setAmount(int $amount)
    {
        //if amount is less than zero
        if (0 > $amount) {
            throw new Exception('The amount should not be less than zero.');
        }

        $this->amount = $amount;
    }

    /**
     * Get 'signature' property.
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set 'signature' property.
     *
     * @param string $signature
     * @return void
     * @throws Exception
     */
    public function setSignature(string $signature)
    {
        //if passed signature’s length is not equal to 32 characters
        if (1 !== preg_match(self::SIGNATURE_PATTERN, $signature)) {
            throw new Exception('Signature incorrect.');
        }

        $this->signature = $signature;
    }

}