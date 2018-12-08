<?php

$inputPath = realpath(dirname(__FILE__) . "/../input/" . basename(__DIR__) . ".txt");
$input = explode("\n", file_get_contents($inputPath));

$part1 = processPart1($input);

echo 'Part 1: ' . implode('', $part1) . "\n";
echo 'Part 2: ' . processPart2($input, $part1) . "\n";

/**
 * @param $input
 * @return array
 */
function processPart1($input)
{
    $steps = getSteps($input);
    $stepOrder = [];
    while (!empty($steps)) {
        $nextStep = getStepWithoutRequirements($steps);
        $stepOrder[$nextStep->getLetter()] = $nextStep->getLetter();
        removeRequirements($steps, $nextStep);
        unset($steps[$nextStep->getLetter()]);
    }

    return $stepOrder;
}

/**
 * @param $input
 * @param $stepOrder
 * @param int $workerNum
 * @param int $baseProcessingTime
 * @return int
 */
function processPart2(
    $input,
    $stepOrder,
    $workerNum = 5,
    $baseProcessingTime = 60
) {
    $workers = Worker::createWorkers($workerNum);
    Step::$baseTimeToProcess = $baseProcessingTime;
    $steps = getSteps($input);
    $timeElapsed = 0;
    $stepsDone = [];

    while (!empty($steps)) {
        $freeWorkerKeys = getFreeWorkers($workers);
        $stepsCanBeProcessed = getProcessableSteps($stepOrder, $steps, $workers);
        foreach ($freeWorkerKeys as $freeWorkerKey) {
            $nextStep = array_shift($stepsCanBeProcessed);
            if (!$nextStep) {
                break;
            }

            $workers[$freeWorkerKey]->setStep($steps[$nextStep]);
        }

        foreach ($workers as $worker) {
            if ($worker->isFree()) {
                continue;
            }

            $worker->decreaseTime();

            if ($worker->isFree()) { // Finished!
                removeRequirements($steps, $worker->getStep());
                unset($stepOrder[$worker->getStepLetter()]);
                unset($steps[$worker->getStepLetter()]);
                $worker->removeStep();
            }
        }

        $timeElapsed++;
    }

    return $timeElapsed;
}

/**
 * @param array $input
 * @return array|Step[]
 */
function getSteps($input)
{
    /** @var array|Step[] $steps */
    $steps = [];
    foreach ($input as $line) {
        if (empty($line)) {
            continue;
        }

        preg_match("/Step (\w) .* step (\w) .*\./", $line, $chars);
        if (!isset($steps[$chars[1]])) {
            $steps[$chars[1]] = new Step($chars[1]);
        }
        if (!isset($steps[$chars[2]])) {
            $steps[$chars[2]] = new Step($chars[2]);
        }

        $steps[$chars[2]]->addRequirements($steps[$chars[1]]);
    }

    ksort($steps);
    return $steps;
}

/**
 * @param Step[] $steps
 * @param array $except
 * @return Step|bool
 */
function getStepWithoutRequirements($steps, $except = [])
{
    foreach ($steps as $step) {
        if (!$step->hasRequirements() && !in_array(
                $step->getLetter(), $except
            )) {
            return $step;
        }
    }

    return false;
}

/**
 * @param Step[] $steps
 * @param Step $requirement
 */
function removeRequirements(&$steps, Step $requirement)
{
    foreach ($steps as $step) {
        if ($step->hasRequirements($requirement)) {
            $step->removeRequirement($requirement);
        }
    }
}

/**
 * @param Worker[] $workers
 * @return array
 */
function getFreeWorkers($workers)
{
    $freeWorkerKeys = [];
    foreach ($workers as $workerKey => $worker) {
        if ($worker->isFree()) {
            $freeWorkerKeys[$workerKey] = $workerKey;
        }
    }

    return $freeWorkerKeys;
}

/**
 * @param array $stepOrder
 * @param array|Step[] $steps
 * @param array|Worker[] $workers
 * @return array
 */
function getProcessableSteps($stepOrder, $steps, $workers)
{
    $processable = [];
    $except = [];

    foreach ($workers as $worker) {
        $stepLetter = $worker->getStepLetter();
        $except[$stepLetter] = $stepLetter;
    }

    foreach ($stepOrder as $nextStep) {
        if (
            !$steps[$nextStep]->hasRequirements()
            && !in_array($nextStep, $except)
        ) {
            $processable[$nextStep] = $nextStep;
            $except[$nextStep] = $nextStep;
        }
    }

    return $processable;
}

class Step
{
    /** @var string */
    protected $letter;

    protected $timeToFinish;
    public static $baseTimeToProcess;

    /** @var array|Step[] */
    protected $requirements = [];

    public function __construct($letter)
    {
        $this->letter = $letter;
        $this->timeToFinish = static::$baseTimeToProcess
            + ord(strtoupper($letter))
            - ord('A')
            + 1;
    }

    public function addRequirements(Step $step)
    {
        $this->requirements[$step->getLetter()] = $step;
    }

    public function hasRequirements(Step $step = null)
    {
        if (is_null($step)) {
            return !empty($this->requirements);
        }

        return isset($this->requirements[$step->getLetter()]);
    }

    public function removeRequirement(Step $step)
    {
        unset($this->requirements[$step->getLetter()]);
    }

    public function getLetter()
    {
        return $this->letter;
    }

    public function getTimeToFinish()
    {
        return $this->timeToFinish;
    }
}

class Worker
{
    /** @var int Time in seconds */
    protected $timeLeft = 0;

    /** @var Step */
    protected $step;

    /** @var bool */
    protected $isFree = true;

    public function setStep(Step $step)
    {
        $this->isFree = false;
        $this->step = $step;
        $this->setTime($step->getTimeToFinish());
    }

    protected function setTime($time)
    {
        $this->timeLeft = $time;
    }

    public function decreaseTime($amount = 1)
    {
        $this->timeLeft -= $amount;
        if ($this->timeLeft <= 0) {
            $this->isFree = true;
        }
    }

    public function removeStep()
    {
        $this->step = null;
    }

    public function getStepLetter()
    {
        return $this->step ? $this->step->getLetter() : '.';
    }

    public function getStep()
    {
        return $this->step;
    }

    public function isFree()
    {
        return $this->isFree;
    }

    /**
     * @param $num
     * @return array|Worker[]
     */
    public static function createWorkers($num)
    {
        $workers = [];
        for ($i = 0; $i < $num; $i++) {
            $workers[$i] = new Worker();
        }

        return $workers;
    }
}