<?php

$inputPath = realpath(dirname(__FILE__) . "/../input/" . basename(__DIR__) . ".txt");
$input = explode("\n", file_get_contents($inputPath));
$points = [];
$minMax = false;

foreach ($input as $line) {
    preg_match("/position=<([\-\s]?\d*), ([\-\s]?\d*)> velocity=<([\-\s]?\d*), ([\-\s]?\d*)>/", $line, $matches);
    $p = [
        'y' => $matches[1],
        'x' => $matches[2],
        'vy' => $matches[3],
        'vx' => $matches[4],
    ];

    $points[] = $p;
    $minMax = getMinMax($p, $minMax);
}

$value = getValue($minMax);

$seconds = 0;
while (true) {
    $minMax = false;
    foreach ($points as &$point) {
        $point['x'] += $point['vx'];
        $point['y'] += $point['vy'];
        $minMax = getMinMax($point, $minMax);
    }

    $currentValue = getValue($minMax);
    if ($currentValue > $value) {
        echo "Part 1:\n";
        foreach ($points as &$point) {
            $point['x'] -= $point['vx'];
            $point['y'] -= $point['vy'];
        }

        for ($x = $minMax['min']['x']; $x <= $minMax['max']['x']; $x++) {
            for ($y = $minMax['min']['y']; $y <= $minMax['max']['y']; $y++) {
                $pointChar = '.';
                foreach ($points as $point) {
                    if ($point['x'] == $x && $point['y'] == $y) {
                        $pointChar = '#';
                    }
                }

                echo $pointChar;
            }
            echo "\n";
        }
        echo "Part 2: " . $seconds;
        exit();
    }

    $value = $currentValue;
    $seconds++;
}

function getMinMax($point, $minMax)
{
    if (!is_array($minMax)) {
        $minMax = [
            'min' => [
                'x' => PHP_INT_MAX,
                'y' => PHP_INT_MAX,
            ],
            'max' => [
                'x' => PHP_INT_MIN,
                'y' => PHP_INT_MIN,
            ],
        ];
    }

    return [
        'min' => [
            'x' => min($point['x'], $minMax['min']['x']),
            'y' => min($point['y'], $minMax['min']['y']),
        ],
        'max' => [
            'x' => max($point['x'], $minMax['max']['x']),
            'y' => max($point['y'], $minMax['max']['y']),
        ],
    ];
}

function getValue($minMax)
{
    return ($minMax['max']['x'] - $minMax['min']['x']) + ($minMax['max']['y'] - $minMax['min']['y']);
}