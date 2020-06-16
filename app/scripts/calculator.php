<?php

require_once __DIR__ . '/../src/Calculator.php';

$options = getopt('a:b:');

if (!array_key_exists('a', $options) || !array_key_exists('b', $options)) {
    die('Please specify operands. Example: -a 1 -b 2 ' . PHP_EOL);
}

$calculator = new Calculator();
echo $calculator->add($options['a'], $options['b']);
echo PHP_EOL;

