<?php

// @TODO: Optimize processing for shorter running

$inputPath = realpath(dirname(__FILE__) . "/../input/" . basename(__DIR__) . ".txt");
$input = file_get_contents($inputPath);

echo "Calulating part 1";
echo "\nPart 1: " . count(processPolymers($input)) . "\n";
echo "Calulating part 2";
echo "\nPart 2: " . removeMostProblematicUnit($input);

function removePolymers(&$polymers)
{
    $foundPolymer = false;
    for ($i = 0; $i < count($polymers) - 1; $i++) {
        if (
            $polymers[$i] != $polymers[$i + 1]
            && strcasecmp($polymers[$i], $polymers[$i + 1]) === 0
        ) {
            $foundPolymer = true;
            unset($polymers[$i]);
            unset($polymers[$i + 1]);
            $i--;
            $polymers = array_values($polymers);
        }
    }

    return $foundPolymer;
}

function processPolymers($polymers)
{
    $polymers = str_split($polymers);

    while (removePolymers($polymers)) {
        echo ".";
    }

    return $polymers;
}

function removeMostProblematicUnit($polymers)
{
    $min = strlen($polymers);

    foreach (getPolimerUnits($polymers) as $char) {
        $polymersNew = str_replace(
            [
                $char,
                strtoupper($char),
            ],
            '',
            $polymers
        );
        $pol = processPolymers($polymersNew);
        if (count($pol) < $min) {
            $min = count($pol);
        }
    }

    return $min;
}

function getPolimerUnits($polymers)
{
    return array_unique(str_split(strtolower($polymers)));
}