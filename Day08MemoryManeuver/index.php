<?php

$inputPath = realpath(dirname(__FILE__) . "/../input/" . basename(__DIR__) . ".txt");
$input = explode(" ", file_get_contents($inputPath));

$rootNode = processNodes($input);

echo "Part 1: " . $rootNode->getTreeMetaEntrySum() . "\n";
echo "Part 2: " . $rootNode->getValue() . "\n";

function processNodes(array &$input)
{
    $node = processNextNodeHeader($input);

    if ($node->getChildNodeQty()) {
        for ($i = 0; $i < $node->getChildNodeQty(); $i++) {
            $childNode = processNodes($input);
            $node->addChildNode($childNode);
        }
    }

    if ($node->getMetaEntryQty()) {
        for ($i = 0; $i < $node->getMetaEntryQty(); $i++) {
            $metaEntry = (int)array_shift($input);
            $node->addMetaEntry($metaEntry);
        }
    }

    return $node;
}

/**
 * @param array $input
 * @return Node
 */
function processNextNodeHeader(array &$input)
{
    $childNodeQty = array_shift($input);
    $metaEntryQty = array_shift($input);
    return new Node($childNodeQty, $metaEntryQty);
}

class Node
{
    /** @var int */
    protected $childNodeQty;

    /** @var int */
    protected $metaEntryQty;

    /** @var array|Node[] */
    protected $childNodes = [];

    /** @var array|int[] */
    protected $metaEntries = [];

    /**
     * Node constructor.
     * @param int $childNodeQty
     * @param int $metaEntryQty
     */
    public function __construct($childNodeQty, $metaEntryQty)
    {
        $this->childNodeQty = $childNodeQty;
        $this->metaEntryQty = $metaEntryQty;
    }

    /**
     * @return int
     */
    public function getChildNodeQty()
    {
        return $this->childNodeQty;
    }

    /**
     * @return int
     */
    public function getMetaEntryQty()
    {
        return $this->metaEntryQty;
    }

    /**
     * @return array|Node[]
     */
    public function getChildNodes()
    {
        return $this->childNodes;
    }

    /**
     * @param Node $childNode
     */
    public function addChildNode(Node $childNode)
    {
        $this->childNodes[count($this->childNodes)+1] = $childNode;
    }

    /**
     * @return array|int[]
     */
    public function getMetaEntries()
    {
        return $this->metaEntries;
    }

    /**
     * @param int $metaEntry
     */
    public function addMetaEntry(int $metaEntry)
    {
        $this->metaEntries[] = $metaEntry;
    }

    /**
     * @return int
     */
    public function getMetaEntrySum()
    {
        return array_sum($this->metaEntries);
    }

    public function getTreeMetaEntrySum()
    {
        $sum = $this->getMetaEntrySum();
        foreach ($this->childNodes as $childNode) {
            $sum += $childNode->getTreeMetaEntrySum();
        }

        return $sum;
    }

    public function getValue()
    {
        if (!$this->childNodeQty) {
            return $this->getMetaEntrySum();
        }

        $value = 0;
        foreach ($this->metaEntries as $metaEntry) {
            if(!isset($this->childNodes[$metaEntry])) {
                continue;
            }

            $value += $this->childNodes[$metaEntry]->getValue();
        }

        return $value;
    }
}