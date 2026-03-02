<?php

$serverName = "localhost";
$username = "root";
$password = "";
$dbName = "";

$connection = new mysqli($serverName, $username, $password, $dbName);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

echo "Connected successfully";
