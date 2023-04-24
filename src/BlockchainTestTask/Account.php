<?php

namespace Ingniq\BlockchainTestTask;

use Exception;

/**
 * Class Account.
 * Imitation of existing accounts.
 *
 * @package Ingniq\BlockchainTestTask
 */
class Account
{
    private static $accounts = array(
        'bob'   => array(
            'id'      => 1,
            'balance' => 0,
        ),
        'alice' => array(
            'id'      => 2,
            'balance' => 0,
        ),
    );

    /**
     * Get account by name.
     *
     * @param string $name
     * @return mixed
     * @throws Exception
     */
    static public function getAccount(string $name)
    {
        $name = strtolower($name);
        if (!isset(self::$accounts[$name])) {
            throw new Exception("Account '{$name}' does not exist.");
        }

        return self::$accounts[$name];
    }

}