<?php

sleep(10);

$file = 'log.txt';
$current = file_get_contents($file);
$current .= date("Y-m-d H:i:s") . "\n";
file_put_contents($file, $current);
