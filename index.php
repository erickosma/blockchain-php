<?php
class Block
{
    private $index;
    private $timestamp;
    private $proof;
    private $previousHash;

    public function __construct($index, $proof, $previous_hash)
    {
        $this->timestamp = time();
        $this->index = $index;
        $this->proof = $proof;
        $this->previousHash = $previous_hash;
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index): void
    {
        $this->index = $index;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp(int $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getProof()
    {
        return $this->proof;
    }

    /**
     * @param mixed $proof
     */
    public function setProof($proof): void
    {
        $this->proof = $proof;
    }

    /**
     * @return mixed
     */
    public function getPreviousHash()
    {
        return $this->previousHash;
    }

    /**
     * @param mixed $previousHash
     */
    public function setPreviousHash($previousHash): void
    {
        $this->previousHash = $previousHash;
    }

}

class Blockchain
{
    public $chain;
    private $difficulty;

    function __construct()
    {
        $this->chain = [];
        $this->difficulty = 7;
        $this->createBlock(1, '0');
    }

    function createBlock($proof, $previous_hash): Block
    {
        $block = new Block($this->getChainLength() + 1, $proof, $previous_hash);
        $this->chain[] = $block;
        return $block;
    }

    function getPreviousBlock() : Block
    {
        return $this->chain[$this->getChainLength() - 1];
    }

    function proofOfWork($previous_proof): int
    {
        $new_proof = 1;
        $check_proof = false;
        $needle = "";
        for ($i = 0; $i < $this->difficulty; $i++) {
            $needle = @$needle . "0";
        }

        while ($check_proof == false) {
            $hash_operation = hash('sha256', $new_proof ** 2 - $previous_proof ** 2);
            $str = substr_count($hash_operation, $needle, 0, $this->difficulty);
            if ($str > 0) {
                $check_proof = true;
            } else {
                $new_proof += 1;
            }
        }
        return $new_proof;
    }

    function hash($block)
    {
        return hash('sha256', json_encode($block));
    }

    function isChainValid($chain): bool
    {
        $previous_block = $chain[0];
        $block_index = 1;
        $chain_length = 0;
        foreach ($chain as $ch) {
            $chain_length += 1;
        }
        for ($i = 0; $i < $this->difficulty; $i++) {
            $needle = @$needle . "0";
        }

        while ($block_index < $chain_length) {
            $block = $chain[$block_index];
            if ($block->getPreviousHash() != $this->hash($previous_block)) {
                return false;
            }

            $previous_proof = $previous_block->getProof();
            $proof = $block->getProof();
            $hash_operation = hash('sha256', $proof ** 2 - $previous_proof ** 2);
            $str = substr_count($hash_operation, $needle, 0, $this->difficulty);

            if (!$str > 0) {
                return false;
            }
            $previous_block = $block;
            $block_index += 1;
        }
        return true;
    }

    function getChainLength(): int
    {
        return count($this->chain);
    }
}


/**
 * @param Blockchain $blockchain
 * @return Block
 */
function getBlock(Blockchain $blockchain): Block
{
    $previous_block = $blockchain->getPreviousBlock();
    $previous_proof = $previous_block->getProof();
    $proof = $blockchain->proofOfWork($previous_proof);
    $previous_hash = $blockchain->hash($previous_block);
    return $blockchain->createBlock($proof, $previous_hash);
}

function printBLock(Blockchain $blockchain)
{
    $block = getBlock($blockchain);
    echo "--- New Block has been created --- \n";
    echo "Index: " . $block->getIndex(). "\n";
    echo "Timestamp: " . $block->getTimestamp() . "\n";
    echo "Proof: " . $block->getProof() . "\n";
    echo "Previous Hash: " . $block->getPreviousHash() . "\n";
    echo "Valid: " . $blockchain->isChainValid($blockchain->chain) . "\n";

}

$start = microtime(true);
$blockchain = new Blockchain();
printBLock($blockchain);
printBLock($blockchain);
printBLock($blockchain);
//var_dump($blockchain->chain);

$time_elapsed_secs = (microtime(true) - $start) ;
echo "--- end --- " . $time_elapsed_secs. "\n";