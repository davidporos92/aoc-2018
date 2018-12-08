<?php

$inputPath = realpath(dirname(__FILE__) . "/../input/" . basename(__DIR__) . ".txt");
$input = explode("\n", file_get_contents($inputPath));

$data = [];
$fabric = [];
$overlap = 0;

foreach ($input as $line) {
    $lineData = parseLine($line);
    $data[] = $lineData;
    $overlap += fillFabric($lineData, $fabric);
}

echo "Part 1: " . $overlap . "\n";

foreach ($data as $lineData) {
    if (!isOverlapping($lineData, $fabric)) {
        echo "Part 2: " . $lineData['id'] . "\n";
    }
}

function parseLine($line)
{
    preg_match("/#(\d*)\s@\s(\d*),(\d*):\s(\d*)x(\d*)/", $line, $data);

    return [
        'id' => $data[1],
        'pos' => [
            'x' => $data[2],
            'y' => $data[3],
        ],
        'size' => [
            'w' => $data[4],
            'h' => $data[5],
        ],
    ];
}

function fillFabric($data, &$fabric)
{
    $coordinates = getCoordinates($data);
    $overlap = 0;

    foreach ($coordinates as $coordinate) {
        if (!isset($fabric[$coordinate['x']][$coordinate['y']])) {
            $fabric[$coordinate['x']][$coordinate['y']] = '#';
        } elseif (
            isset($fabric[$coordinate['x']][$coordinate['y']]) &&
            $fabric[$coordinate['x']][$coordinate['y']] == '#'
        ) {
            $fabric[$coordinate['x']][$coordinate['y']] = 'X';
            $overlap++;
        }
    }

    return $overlap;
}

function isOverlapping($data, $fabric)
{
    $coordinates = getCoordinates($data);

    foreach ($coordinates as $coordinate) {
        if (
            isset($fabric[$coordinate['x']][$coordinate['y']]) &&
            $fabric[$coordinate['x']][$coordinate['y']] == 'X'
        ) {
            return true;
        }
    }

    return false;
}

function getCoordinates($data)
{
    $coordinates = [];

    for ($x = $data['pos']['x']; $x < $data['pos']['x'] + $data['size']['w']; $x++) {
        for ($y = $data['pos']['y']; $y < $data['pos']['y'] + $data['size']['h']; $y++) {
            $coordinates[] = [
                'x' => $x,
                'y' => $y,
            ];
        }
    }

    return $coordinates;
}