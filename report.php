<?php
// check_data_availability.php

// Get the month and year from the GET request
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// Define the path where the data is stored
$path = "pts_db/" . $year . "/" . $month;

// Check if the folder or data file exists
if (is_dir($path) && count(glob($path . "/*")) > 0) {
    echo "data found"; // Data exists
} else {
    echo "no data"; // Data doesn't exist
}
?>
