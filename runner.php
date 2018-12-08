<?php

require_once "bootstrap.php";

echoColorized("\n********** Welcome to Advent of code 2018! **********", COLOR_GREEN);

$days = glob("Day*", GLOB_ONLYDIR);

do {
    echoColorized(
        "\nPlease select which day you want to run by entering the days number. "
        . "To exit just press enter.",
        COLOR_GREEN
    );

    foreach ($days as $key => $day) {
        echoColorized("[" . ($key + 1) . "] " . $day, COLOR_YELLOW);
    }

    $userInput = getUserInput();
    if ((empty($userInput) && $userInput != "0") || in_array($userInput, USER_INPUT_EXIT_CHARS, true)) {
        break;
    }

    $userInput = (int)$userInput - 1;

    if (!isset($days[$userInput])) {
        echoColorized("!!!!! Please select an existing entry! !!!!!\n", COLOR_RED);
        continue;
    }

    $dayBasePath = realpath(dirname(__FILE__) . '/' . $days[$userInput]);
    $info = json_decode(file_get_contents($dayBasePath . '/info.json'));

    echoColorized("\nYou have selected day " . $info->day . " - " . $info->title, COLOR_CYAN);
    if (property_exists($info, 'additionalInfo')) {
        echoColorized($info->additionalInfo, COLOR_CYAN);
    }
    include $dayBasePath . '/' . $info->mainFile;
} while (true);

echoColorized("Bye!", COLOR_GREEN);