<?php

$inputPath = realpath(dirname(__FILE__) . "/../input/" . basename(__DIR__) . ".txt");
$input = explode("\n", file_get_contents($inputPath));

$coordinates = [];
$map = [];
$max = [
    'x' => 0,
    'y' => 0,
];

mapCoordinatesTo($input, $coordinates, $map, $max);
designateClosestCoordinatesToPoints($coordinates, $map, $max);
$infiniteCoords = getInfiniteCoords($map, $max);
$coordsCount = array_count_values($map);
asort($coordsCount);
foreach ($infiniteCoords as $infiniteCoord) {
    unset($coordsCount[$infiniteCoord]);
}

echo "Part 1: " . max($coordsCount) . "\n";
echo "Part 2: " . count(
        getFieldsWithTotalDistanceLessThan(10000, $coordinates, $max)
    ) . "\n";

/**
 * @param int $x
 * @param int $y
 * @return string
 */
function generateCoordinate($x, $y)
{
    return $x . '-' . $y;
}

function mapCoordinatesTo($coords, &$coordinates, &$map, &$max)
{
    foreach ($coords as $coordinate) {
        $coordinate = explode(', ', $coordinate);
        $x = (int)$coordinate[0];
        $y = (int)$coordinate[1];
        $coord = generateCoordinate($x, $y);

        $coordinates[$coord] = [
            'x' => $x,
            'y' => $y,
            'closestCount' => 0,
        ];

        $map[$coord] = $coord;

        if ($max['x'] < $x) {
            $max['x'] = $x;
        }

        if ($max['y'] < $y) {
            $max['y'] = $y;
        }
    }
}

function designateClosestCoordinatesToPoints($coordinates, &$map, $max)
{
    for ($x = 0; $x <= $max['x']; $x++) {
        for ($y = 0; $y <= $max['y']; $y++) {
            $coord = generateCoordinate($x, $y);
            if (isset($coordinates[$coord])) {
                continue;
            }

            $closest = [
                'coord' => null,
                'distance' => -1,
                'count' => 0,
            ];
            foreach ($coordinates as $actualCoord => $coordinate) {
                $distance = abs($coordinate['x'] - $x) + abs(
                        $coordinate['y'] - $y
                    );
                if ($distance < $closest['distance'] || $closest['distance'] === -1) {
                    $closest['coord'] = $actualCoord;
                    $closest['distance'] = $distance;
                    $closest['count'] = 0;
                }

                if ($distance == $closest['distance']) {
                    $closest['count']++;
                }
            }

            $map[$coord] = $closest['count'] == 1 ? $closest['coord'] : '.';
        }
    }
}

function getInfiniteCoords($map, $max)
{
    $infiniteCoords = [];

    for ($x = 0; $x <= $max['x']; $x++) {
        $coord = generateCoordinate($x, 0);
        $infiniteCoords[$map[$coord]] = $map[$coord];
    }

    for ($x = 0; $x <= $max['x']; $x++) {
        $coord = generateCoordinate($x, $max['y']);
        $infiniteCoords[$map[$coord]] = $map[$coord];
    }

    for ($y = 0; $y <= $max['y']; $y++) {
        $coord = generateCoordinate(0, $y);
        $infiniteCoords[$map[$coord]] = $map[$coord];
    }

    for ($y = 0; $y <= $max['y']; $y++) {
        $coord = generateCoordinate($max['x'], $y);
        $infiniteCoords[$map[$coord]] = $map[$coord];
    }

    return $infiniteCoords;
}

function getTotalDistance($coordinates, $x, $y)
{
    $totalDistance = 0;
    foreach ($coordinates as $coordinate) {
        $totalDistance += (abs($coordinate['x'] - $x) + abs(
                $coordinate['y'] - $y
            ));
    }

    return $totalDistance;
}

function getFieldsWithTotalDistanceLessThan($limit, $coordinates, $max)
{
    $fields = [];

    for ($x = 0; $x <= $max['x']; $x++) {
        for ($y = 0; $y <= $max['y']; $y++) {
            $totalDistance = getTotalDistance($coordinates, $x, $y);
            if ($totalDistance < $limit) {
                $coords = generateCoordinate($x, $y);
                $fields[$coords] = $coords;
            }
        }
    }

    return $fields;
}