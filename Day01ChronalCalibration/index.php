<?php

$inputPath = realpath(dirname(__FILE__) . "/../input/" . basename(__DIR__) . ".txt");
$input = explode("\n", file_get_contents($inputPath));

$frequency = 0;
$frequencyHits = [$frequency => 1];
$frequencyHitTwice = false;
$part1 = false;

while (!$part1 || !$frequencyHitTwice) {
    foreach ($input as $line) {
        $frequency = getNewFrequency($line, $frequency);
        $frequencyHits[$frequency] = isset($frequencyHits[$frequency])
            ? $frequencyHits[$frequency] + 1
            : 1;

        if ($frequencyHits[$frequency] == 2 && !$frequencyHitTwice) {
            $frequencyHitTwice = $frequency;
        }
    }

    if (!$part1) {
        $part1 = true;
        echo "Part 1: " . $frequency . "\n";
    }

    if ($frequencyHitTwice) {
        echo "Part 2: " . $frequencyHitTwice . "\n";
    }
}

function getNewFrequency($line, $frequency)
{
    preg_match("/(\+|\-)(\d*)/", $line, $frequencyChange);
    switch ($frequencyChange[1]) {
        case '+':
            $frequency += $frequencyChange[2];
            break;
        case '-':
            $frequency -= $frequencyChange[2];
            break;
    }

    return $frequency;
}