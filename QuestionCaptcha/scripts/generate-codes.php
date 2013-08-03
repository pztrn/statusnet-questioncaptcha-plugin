#!/usr/bin/env php
<?php

// CLI Options
$shortoptions = 'c:l:h';
$opts = getopt($shortoptions);
$codes = array();
var_dump($opts);

// Inits :)
$length = null;
$count = null;

// Function to get random strings.
function generate_random_strings($length)
{
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

// Print help :)
function help()
{
    echo "QuestionCaptcha Codes generator.
Generates random codes for QuestionCaptcha.

Available options:

    -h              Show this help.
    -c [count]      Codes to generate. You should choose at least 5
                    codes to generate.
    -l [length]     Codes length in characters. Recommended at least
                    10.
";
}

// Main action goes here.

// User wanna help?
if (count($opts) == 0 or in_array("h", $opts))
{
    help();
    exit(0);
}

// Codes count. Defaulting to 5, if not specified.
if (array_key_exists("c", $opts))
{
    $count = $opts["c"];
}
else
{
    $count = 5;
}

// Calculating length. If not specified - defaulting to 10.
if (array_key_exists("l", $opts))
{
    $length = $opts["l"];
}
else
{
    $length = 10;
}

// Just some useful output.
echo "About to generate '{$count}' codes with '{$length}' characters length each...\n\n";

// First, we should check that user wants to do something.
foreach (range(1, $count) as $num)
{
    $code = "";
    // We should generate 3 random codes
    foreach (range(1, 3) as $codecount)
    {
        if ($codecount == 1)
        {
            $code = $code . generate_random_strings($length);
        }
        else
        {
            $code = $code . "," . generate_random_strings($length);
        }
    }
    $codes[$num] = $code;
}

// Out to user.
echo "Count: {$count}, Length: {$length}\n";
echo "Add these lines into config.php:

addPlugin('QuestionCaptcha', array(
                  'codes' => array(";

foreach ($codes as $i => $value)
{
    if ($i == 1)
    {
        echo "'$i' => '$value',\n";
    }
    else if ($i == $count)
    {
        echo "                                   '$i' => '$value',))\n";
    }
    else
    {
        echo "                                   '$i' => '$value',\n";
    }
}
echo ");\n"

?>
