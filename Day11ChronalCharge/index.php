<?php

$input = 8868;
$powerCellGridSize = 300;
$powerCells = [];
$maxPowerLevelGrid = [
    'gridStart' => [
        'x' => null,
        'y' => null,
    ],
    'power' => PHP_INT_MIN,
];

for ($x = 1; $x <= $powerCellGridSize; $x++) {
    for ($y = 1; $y <= $powerCellGridSize; $y++) {
        $powerCells[$x][$y] = getPowerLevel($x, $y, $input);
        if ($x >= 3 && $y >= 3) {
            $coordinates = getCoordinatesForSize($x, $y, 3, true);
            $gridPowerLevel = getPowerLevelForCoordinates($powerCells, $coordinates);

            if ($maxPowerLevelGrid['power'] < $gridPowerLevel) {
                $maxPowerLevelGrid = [
                    'gridStart' => [
                        'x' => $x - 2,
                        'y' => $y - 2,
                    ],
                    'power' => $gridPowerLevel,
                ];
            }
        }
    }
}

echo "Part 1: " . $maxPowerLevelGrid['gridStart']['x'] . ',' . $maxPowerLevelGrid['gridStart']['y'] . "\n";

$maxPowerLevelGrid = [
    'gridStart' => [
        'x' => null,
        'y' => null,
    ],
    'size' => null,
    'power' => PHP_INT_MIN,
];

for ($x = 1; $x <= $powerCellGridSize; $x++) {
    for ($y = 1; $y <= $powerCellGridSize; $y++) {
        for ($size = 1; $size <= min($powerCellGridSize - $x, $powerCellGridSize - $y); $size++) {
            $coordinates = getCoordinatesForSize($x, $y, $size);
            $gridPowerLevel = getPowerLevelForCoordinates($powerCells, $coordinates);

            if ($maxPowerLevelGrid['power'] < $gridPowerLevel) {
                $maxPowerLevelGrid = [
                    'gridStart' => [
                        'x' => $x,
                        'y' => $y,
                    ],
                    'size' => $size,
                    'power' => $gridPowerLevel,
                ];
            }
        }
    }
}

echo "Part 2: " . $maxPowerLevelGrid['gridStart']['x'] . ',' . $maxPowerLevelGrid['gridStart']['y'] . ',' . $maxPowerLevelGrid['size'] . "\n";

function getPowerLevel($x, $y, $serialNumber)
{
    $rackId = $x + 10;
    return ((int)substr((($rackId * $y) + $serialNumber) * $rackId, -3, 1)) - 5;
}

function getCoordinatesForSize($x, $y, $size, $backwards = false)
{
    $xs = [];
    $ys = [];

    for ($i = 0; $i < $size; $i++) {
        $xs[] = $backwards ? $x - $i : $x + $i;
        $ys[] = $backwards ? $y - $i : $y + $i;
    }

    return [
        'x' => $xs,
        'y' => $ys,
    ];
}

function getPowerLevelForCoordinates($powerCells, $coordinates)
{
    $powerLevel = 0;
    foreach ($coordinates['x'] as $coordinateX) {
        foreach ($coordinates['y'] as $coordinateY) {
            $powerLevel += $powerCells[$coordinateX][$coordinateY];
        }
    }

    return $powerLevel;
}