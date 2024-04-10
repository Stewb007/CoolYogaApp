<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

header('Content-Type: application/json');

$classes = [];
$file = fopen('classes.csv', 'r');
while (($line = fgetcsv($file)) !== false) {
    $classes[] = $line;
}
fclose($file);

echo json_encode($classes);
?>
