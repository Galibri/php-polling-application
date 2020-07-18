<?php
$servername      = "localhost";
$username        = "root";
$password        = "admin";
$dbname          = "polling";
$site_url_suffix = "polling/";

try {

    $conn = new PDO("mysql:host=$servername;dbname=" . $dbname, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    echo "<h2>Connection failed: " . $e->getMessage() . "</h2>";
    die();

}