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
        //Initialization double linked list.
        $chain = new SplDoublyLinkedList();

        //Set mode 'IT_MODE_LIFO' (The list will be iterated in a last in, first out order, like a stack).
        $chain->setIteratorMode(SplDoublyLinkedList::IT_MODE_LIFO);

        //Building a chain from the lowest node to the root node.
        $node = $this->getLowerNode();
        $chain->push($node);

        $rootNode = $this->getRoot();
        while ($node->getBlock()->getId() !== $rootNode->getBlock()->getId()) {
            $node = $this->getNode($node->getParentId());

            $chain->push($node);
        }

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
    public function addBlock($parentId, Block $block)
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
     * @param int   $parentId
     * @param Block $block
     * @return void
     */
    private function addNode($parentId, Block $block)
    {
        $node = new BlockTreeNode($parentId, $block);

        if (null === $parentId) {
            //Adding a root node with level 0.
            $this->root = $node;
            $node->setLevel(0);
        } else {
            //Adding a node.
            $parentNode = $this->getNode($parentId);
            $node->setLevel($parentNode->getLevel() + 1);
        }

        //Setting the depth of the tree.
        if ($this->depth < $node->getLevel()) {
            $this->depth = $node->getLevel();
        }

        //Adding a node to the node list.
        $this->nodes[$block->getId()] = $node;

        //Updating tree map.
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
    private function updatingTree($parentId, $blockId)
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
        if (!$this->getRoot()) {
            throw new Exception("The blocks tree is not built.");
        }
        $rootId = $this->getRoot()->getBlock()->getId();

        //Initialization the stack.
        $stackIds = new SplStack();

        //Start at the root.
        $stackIds->push($rootId);

        //Mark on the visit.
        $visited          = array();
        $visited[$rootId] = true;

        //Handling a non-empty stack. Last in, first out. Until we get to the lowest node.
        while (!$stackIds->isEmpty() && $this->nodes[$stackIds->top()]->getLevel() != $this->depth) {
            //Taking a vertex from the stack.
            $itemId = $stackIds->pop();

            if (!empty($this->treemap[$itemId])) {
                //Push all child vertices not visited on the stack.
                foreach ($this->treemap[$itemId] as $blockId) {
                    if (!$visited[$itemId][$blockId]) {
                        $stackIds->push($blockId);
                        $visited[$blockId] = true;
                    }
                }
            }
        }

        //Return lower node.
        return $this->nodes[$stackIds->pop()];
    }
}