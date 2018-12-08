<?php

function echoColorized($text, $color = 'default', $addNewLine = true)
{
    $colors = [
        COLOR_DEFAULT => "\e[39m",
        COLOR_BLACK => "\e[30m",
        COLOR_RED => "\e[31m",
        COLOR_GREEN => "\e[32m",
        COLOR_YELLOW => "\e[33m",
        COLOR_BLUE => "\e[34m",
        COLOR_CYAN => "\e[36m",
    ];

    if(!isset($colors[$color]))
    {
        $color = 'default';
    }

    echo $colors[$color].$text.$colors['default'].($addNewLine?"\n":'');
}

function getUserInput()
{
    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    fclose($handle);

    return strtolower(trim($line));
}