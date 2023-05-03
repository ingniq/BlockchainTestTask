<?php

namespace Ingniq\BlockchainTestTask;

use Exception;
use SplDoublyLinkedList;
use SplStack;

/**
 * Class BlockTree.
 * A tree of Block objects with a single root. Every Block has a single ancestor and unlimited number of descendants.
 * @package Ingniq\BlockchainTestTask
 */
class BlockTree
{
    /**
     * Root node. There can only be one.
     *
     * @var BlockTreeNode|null
     */
    private $root;
    /**
     * Tree structure.
     *
     * @var array
     */
    private $treemap;
    /**
     * List of nodes.
     *
     * @var array
     */
    private $nodes;
    /**
     * The depth of the tree.
     *
     * @var int
     */
    private $depth;

    /**
     * BlockTree constructor.
     *
     * Parameter initialization.
     */
    public function __construct()
    {
        $this->root    = null;
        $this->treemap = null;
        $this->nodes   = null;
        $this->depth   = 0;
    }

    /**
     * Get 'id' parameter.
     *
     * @param int $id
     * @return BlockTreeNode|null
     */
    public function getNode($id)
    {
        return isset($this->nodes[$id]) ? $this->nodes[$id] : null;
    }

    /**
     * Get 'root' parameter.
     *
     * @return BlockTreeNode|null
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Get a list of Blocks within the longest chain of Blocks in the Block Tree.
     *
     * @return SplDoublyLinkedList
     * @throws Exception
     */
    public function getLongestChain()
    {
        $chain = new SplDoublyLinkedList();

        //The list will be iterated in a last in, first out order, like a stack.
        $chain->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO);

        $node = $this->getLowerNode();
        $chain->push($node);

        $rootNode = $this->getRoot();
        while ($node->getBlock()->getId() !== $rootNode->getBlock()->getId()) {
            $node = $this->getNode($node->getParentId());

            $chain->push($node);
        }

        // A chain of nodes from the root to the lowest node in the block tree.
        return $chain;
    }

    /**
     * Adding block.
     * Every Block will have only 1 ancestor and the unlimited number of descendants.
     * There can be only 1 tree root – the Block without any parents.
     *
     * @param int|null $parentId
     * @param Block    $block
     * @return void
     * @throws Exception
     */
    public function addBlock(?int $parentId, Block $block)
    {
        //There can be only 1 tree root.
        if (null === $parentId && $this->getRoot()) {
            throw new Exception('Tree root already exists. There can only be 1 tree root.');
        }

        //Every Block will have only 1 ancestor.
        if (isset($this->treemap[$block->getId()])) {
            throw new Exception('Block already exists.');
        }

        $this->addNode($parentId, $block);
    }

    /**
     * Creating new tree node.
     *
     * @param int|null   $parentId
     * @param Block $block
     * @return void
     */
    private function addNode(?int $parentId, Block $block)
    {
        // If $parentId is null, then it means insertion into the root of the tree.
        $node = BlockTreeNode::createByParameters($parentId, $block);

        if (null === $parentId) {
            $this->root = $node;
            $node->setLevel(0);
        } else {
            $parentNode = $this->getNode($parentId);
            $node->setLevel($parentNode->getLevel() + 1);
        }

        $this->depth = $node->getLevel();
        $this->nodes[$block->getId()] = $node;

        if (null !== $parentId) {
            $this->updatingTree($parentId, $block->getId());
        }
    }

    /**
     * Updating tree map.
     *
     * @param int $parentId
     * @param int $blockId
     * @return void
     */
    private function updatingTree(int $parentId, int $blockId)
    {
        $this->treemap[$parentId][] = $blockId;
    }

    /**
     * Getting the lowest node by Depth-First-Search method
     *
     * @return BlockTreeNode
     * @throws Exception
     */
    public function getLowerNode()
    {
        // TODO: Check the optimality and correctness of the implementation of the selected algorithm
        if (!$this->getRoot()) {
            throw new Exception("The blocks tree is not built.");
        }

        $rootBlockId = $this->getRoot()->getBlock()->getId();
        $blocksForProcessingStack = new SplStack();

        //Start at the root.
        $blocksForProcessingStack->push($rootBlockId);

        //Mark on the visit.
        $visited          = array();  // TODO: сheck whether to use this variable
        $visited[$rootBlockId] = true;

        //Handling a non-empty stack. Last in, first out. Until we get to the lowest node.
        while (!$blocksForProcessingStack->isEmpty() && $this->nodes[$blocksForProcessingStack->top()]->getLevel() != $this->depth) {
            $parentBlockId = $blocksForProcessingStack->pop();

            if (!empty($this->treemap[$parentBlockId])) {
                //Push all child vertices not visited on the stack.
                foreach ($this->treemap[$parentBlockId] as $blockId) {
                    if (empty($visited[$blockId])) {
                        $blocksForProcessingStack->push($blockId);
                        $visited[$blockId] = true;
                    }
                }
            }
        }

        //Return lower node.
        return $this->nodes[$blocksForProcessingStack->pop()];
    }
}