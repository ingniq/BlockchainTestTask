<?php

namespace Ingniq\BlockchainTestTask;

/**
 * Class BlockTreeNode.
 * A class object is a tree node with a parent pointer, a depth value in the tree, and a block.
 * @package Ingniq\BlockchainTestTask
 */
class BlockTreeNode
{
    /**
     * Id of the parent in the block tree.
     *
     * @var int
     */
    private $parentId;
    /**
     * Block as part of node.
     *
     * @var Block
     */
    private $block;
    /**
     * Node depth in block tree.
     *
     * @var int
     */
    private $level;

    /**
     * BlockTreeNode constructor.
     *
     * Parameter initialization.
     * @param int   $parentId
     * @param Block $block
     */
    private function __construct(?int $parentId, Block $block)
    {
        $this->parentId = $parentId;
        $this->block    = $block;
        $this->level    = 0;
    }

    /**
     * BlockTreeNode static method-factory.
     *
     * @param int   $parentId
     * @param Block $block
     */
    public static function createByParameters(?int $parentId, Block $block)
    {
        return new self($parentId, $block);
    }

    /**
     * Get 'block' parameter.
     *
     * @return Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Get 'level' parameter.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set 'level' parameter.
     *
     * @param int $level
     * @return void
     */
    public function setLevel(int $level)
    {
        $this->level = $level;
    }

    /**
     * Get 'parentId' parameter.
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }
}