<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

header('Content-Type: application/json');

// Decode JSON from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Assign variables from the decoded JSON
$className = $data['className'];
$category = $data['category'];
$time = $data['time'];
$location = $data['location'];
$spots = $data['spots'];

// Prepare the class data
$classData = array($time, $className, $category, $location, $spots);

// Open the file in append mode
$file = fopen('classes.csv', 'a');

// Write the class data to the file
fputcsv($file, $classData);

// Close the file
fclose($file);

// Prepare and send the response
$response = [
    'success' => true,
    'message' => 'Class saved successfully',
];

echo json_encode($response);
?>
