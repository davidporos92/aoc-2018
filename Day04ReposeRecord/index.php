<?php

require_once realpath(dirname(__FILE__) . "/../vendor/autoload.php");

$inputPath = realpath(dirname(__FILE__) . "/../input/" . basename(__DIR__) . ".txt");
$input = explode("\n", file_get_contents($inputPath));

$lines = [];
foreach ($input as $line) {
    preg_match("/\[(.*)\]/", $line, $dateMatch);
    $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $dateMatch[1]);

    $lines[$date->format('mdHi')] = [
        'line' => $line,
        'date' => $date,
    ];
}

ksort($lines);

$guard = null;
/** @var Guard[] $guards */
$guards = [];
foreach ($lines as $line) {
    preg_match("/\#(\d*)/", $line['line'], $guardIdMatch);

    if (isset($guardIdMatch[1]) && !isset($guards[$guardIdMatch[1]])) {
        $guard = new Guard($guardIdMatch[1]);
        $guards[$guardIdMatch[1]] = $guard;
        continue;
    } elseif (isset($guardIdMatch[1]) && isset($guards[$guardIdMatch[1]])) {
        $guard = $guards[$guardIdMatch[1]];
    }

    preg_match("/(wakes up|falls asleep)/", $line['line'], $activityMatch);
    if (isset($activityMatch[1])) {
        $activity = new GuardActivity($line['date'], $activityMatch[1]);
        $guard->addActivity($activity);
    }
}

$part1 = null;
$part2 = null;
foreach ($guards as $guard) {
    $guard->countSleepMinutes();

    if (is_null($part1)) {
        $part1 = $guard;
        continue;
    }

    if (is_null($part2)) {
        $part2 = $guard;
        continue;
    }

    if ($part1->getSumSleepMinutes() < $guard->getSumSleepMinutes()) {
        $part1 = $guard;
    }

    if ($part2->getMostSleepMinuteCount() < $guard->getMostSleepMinuteCount()) {
        $part2 = $guard;
    }
}

echo "Part 1: " . (int)$part1->getId() * (int)$part1->getMostSleepMinute() . "\n";
echo "Part 2: " . (int)$part2->getId() * (int)$part2->getMostSleepMinute() . "\n";

class Guard
{
    /** @var string */
    protected $id;

    /** @var array|GuardActivity[] */
    protected $activities = [];

    protected $sleepMinutes = [];
    protected $sumSleepMinutes = 0;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function addActivity(GuardActivity $activity)
    {
        $this->activities[$activity->getDate()->format('mdHi')] = $activity;
    }

    public function sortActivities()
    {
        ksort($this->activities);
    }

    public function countSleepMinutes()
    {
        $this->sortActivities();

        $sleepActivity = null;
        $awakeActivity = null;
        foreach ($this->activities as $activity) {
            if ($activity->isFallsAsleep()) {
                $sleepActivity = clone $activity;
            }

            if ($activity->isWakesUp()) {
                $awakeActivity = clone $activity;
            }

            if (!is_null($sleepActivity) && !is_null($awakeActivity)) {
                $this->countSleepMinutesBetweenDates(
                    $sleepActivity->getDate(),
                    $awakeActivity->getDate()
                );

                $sleepActivity = null;
                $awakeActivity = null;
            }
        }
    }

    public function getSleepMinutes()
    {
        return $this->sleepMinutes;
    }

    public function getSumSleepMinutes()
    {
        return $this->sumSleepMinutes;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getActivities()
    {
        return $this->activities;
    }

    public function getMostSleepMinute()
    {
        return array_keys($this->sleepMinutes, $this->getMostSleepMinuteCount())[0];
    }

    public function getMostSleepMinuteCount()
    {
        if (empty($this->sleepMinutes)) {
            return 0;
        }

        return max($this->sleepMinutes);
    }

    protected function countSleepMinutesBetweenDates(
        \Carbon\Carbon $sleepDate,
        \Carbon\Carbon $awakeDate
    ) {
        while (
            $sleepDate->format('mdHi')
            < $awakeDate->format('mdHi')
        ) {
            if ($sleepDate->format('mdHi') == $awakeDate->format('mdHi')) {
                break;
            }

            if (!isset($this->sleepMinutes[$sleepDate->format('Hi')])) {
                $this->sleepMinutes[$sleepDate->format('Hi')] = 0;
            }

            $this->sleepMinutes[$sleepDate->format('Hi')]++;
            $this->sumSleepMinutes++;

            $sleepDate->addMinute();
        }
    }
}

class GuardActivity
{
    /** @var \Carbon\Carbon */
    protected $date;

    /** @var string */
    protected $name;

    public function __construct(\Carbon\Carbon $date, $name)
    {
        $this->date = $date;
        $this->name = $name;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function isFallsAsleep()
    {
        return $this->name == 'falls asleep';
    }

    public function isWakesUp()
    {
        return $this->name == 'wakes up';
    }
}