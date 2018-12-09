<?php

ini_set('memory_limit', '4096M');
set_time_limit(10000);

$inputPath = realpath(dirname(__FILE__) . "/../input/" . basename(__DIR__) . ".txt");
$input = file_get_contents($inputPath);
preg_match("/(\d*) players; last marble is worth (\d*) points/", $input, $matches);

$playersQty = $matches[1];
$lastMarblesWorth = $matches[2];

echo "Part 1: " . getMaxPlayerScore(play($playersQty, $lastMarblesWorth)) . "\n";
echo "Part 2: Comment out this part if you want to try to run it on your computer!\n"; // . getMaxPlayerScore(play($playersQty, $lastMarblesWorth*100)) . "\n";

function play($playersQty, $lastMarblesWorth)
{
    $actualPlayerIndex = 1;
    $players = initPlayers($playersQty);
    $currentMarble = addMarble(0);

    for ($i = 1; $i <= $lastMarblesWorth; $i++) {
        if ($i % 100000 == 0) {
            echo $i . "\n";
        }
        if ($i % 23 != 0) {
            $currentMarble = $currentMarble->getNext()->insert($i);
            $actualPlayerIndex = getNextPlayerIndex($actualPlayerIndex, $players);
            continue;
        }

        $marbleToRemove = $currentMarble
            ->getPrevious()
            ->getPrevious()
            ->getPrevious()
            ->getPrevious()
            ->getPrevious()
            ->getPrevious()
            ->getPrevious();
        $players[$actualPlayerIndex]->addScore($i);
        $players[$actualPlayerIndex]->addScore($marbleToRemove->getValue());

        $currentMarble = $marbleToRemove->delete();
    }

    return $players;
}

function printPlayers($players)
{
    foreach ($players as $index => $player) {
        echo "[" . $index . "] " . $player->getScore() . "\n";
    }
}

/**
 * @param int $playersQty
 * @return array|Player[]
 */
function initPlayers(int $playersQty)
{
    $players = [];
    for ($i = 1; $i <= $playersQty; $i++) {
        $players[$i] = new Player();
    }

    return $players;
}

/**
 * @param int $actualPlayerIndex
 * @param Player[]|array $players
 * @return int
 */
function getNextPlayerIndex(int $actualPlayerIndex, array $players)
{
    $actualPlayerIndex++;
    if (count($players) < $actualPlayerIndex) {
        return 1;
    }

    return $actualPlayerIndex;
}

/**
 * @param Player[]|array $players
 * @return int
 */
function getMaxPlayerScore($players)
{
    $max = -1;
    foreach ($players as $player) {
        if ($player->getScore() > $max) {
            $max = $player->getScore();
        }
    }

    return $max;
}

function addMarble($value)
{
    $marble = new Marble($value);
    $marble->setNext($marble);
    $marble->setNext($marble);
    return $marble;
}

class Marble
{
    /** @var int */
    protected $value;

    /** @var Marble */
    protected $previous;

    /** @var Marble */
    protected $next;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function setNext(Marble &$marble)
    {
        $this->next = $marble;
    }

    public function setPrevious(Marble &$marble)
    {
        $this->previous = $marble;
    }

    /**
     * @return Marble
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @return Marble
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    public function insert($value)
    {
        $marble = new Marble($value);
        $marble->setPrevious($this);
        $marble->setNext($this->next);

        $this->getNext()->setPrevious($marble);
        $this->setNext($marble);

        return $marble;
    }

    public function delete()
    {
        $this->getPrevious()->setNext($this->next);
        $this->getNext()->setPrevious($this->previous);

        return $this->next;
    }
}

class Player
{
    /** @var int */
    protected $score = 0;

    public function addScore(int $score)
    {
        $this->score += $score;
    }

    public function getScore()
    {
        return $this->score;
    }
}