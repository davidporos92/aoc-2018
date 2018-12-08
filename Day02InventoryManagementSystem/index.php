<?php

$inputPath = realpath(dirname(__FILE__) . "/../input/" . basename(__DIR__) . ".txt");
$input = explode("\n", str_replace("\r", "", file_get_contents($inputPath)));

$twoCount = 0;
$threeCount = 0;
foreach ($input as $line) {
    $chars = count_chars($line);

    if(array_search(2, $chars))
    {
        $twoCount++;
    }
    if(array_search(3, $chars))
    {
        $threeCount++;
    }
}

echo "Part 1: " . $twoCount * $threeCount . "\n";

$boxId = "";
for($i = 0; $i < count($input) - 1; $i++) {
    $charsOfI = str_split($input[$i]);

    for($j = $i+1; $j < count($input); $j++) {
        $charsOfJ = str_split($input[$j]);

        foreach ($charsOfI as $charPos => $char) {
            if($charsOfJ[$charPos] == $char) {
                $boxId .= $char;
            }
        }

        if(strlen($boxId) + 1 == count($charsOfI)) {
            echo "Part 2: " . $boxId . "\n";
            exit(0);
        }

        $boxId = "";
    }
}