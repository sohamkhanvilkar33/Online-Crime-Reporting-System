a<?php

$env = parse_ini_file(__DIR__ . '/../.env');

$host = $env['DB_HOST'];
$username = $env['DB_USERNAME'];
$password = $env['DB_PASSWORD'];
$database = $env['DB_DATABASE'];

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?><?php

$host = "localhost";
$username = "ocrs_user";
$password = "Soham@2004";
$database = "online_crime_reporting";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>
