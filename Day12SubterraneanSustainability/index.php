<?php

$inputPath = realpath(dirname(__FILE__) . "/../input/" . basename(__DIR__) . ".txt");
$input = explode("\n", file_get_contents($inputPath));
$firstLine = true;
$potsState = "";
$patterns = [];
$generationsToSimulate = 20;
$actualGeneration = 0;

foreach ($input as $line) {
    $line = trim($line);
    if ($firstLine) {
        $firstLine = false;
        preg_match("/initial state: ([\.#]*)/", $line, $matches);
        $potsState = $matches[1];
        continue;
    }

    if (empty($line)) {
        continue;
    }

    preg_match("/([.#]{5})\s=>\s([.#])/", $line, $matches);

    $patterns[$matches[1]] = $matches[2];
}

$potsState = str_split($potsState);
echo $actualGeneration . ": " . implode('', $potsState) . "\n";
for ($i = 0; $i < $generationsToSimulate; $i++) {
    $newState = [];
    $sum = 0;

    $minPot = min(array_keys($potsState));
    for ($key = $minPot - 2; $key < count($potsState); $key++) {
        $potPattern =
            ($potsState[$key - 2] ?? '.')
            . ($potsState[$key - 1] ?? '.')
            . ($potsState[$key] ?? '.')
            . ($potsState[$key + 1] ?? '.')
            . ($potsState[$key + 2] ?? '.');

        if(isset($patterns[$potPattern])) {
            $newState[$key] = $patterns[$potPattern];
        } elseif(isset($potsState[$key])) {
            $newState[$key] = '.';
        }

        if (isset($newState[$key]) && $newState[$key] == '#') {
            $sum += $key;
        }
    }

    $potsState = $newState;
    echo ++$actualGeneration . ": " . implode('', $potsState) . "\n";
}

echo "Part 1: " . $sum . "\n";
