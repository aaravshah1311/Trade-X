<?php
// ENABLE ERRORS TO DEBUG THE 500 ERROR
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "sql112.infinityfree.com"; // e.g., sqlXXX.epizy.com
$user = "if0_40487831";
$pass = "x83vEaUEnUhQoh";
$dbname = "if0_40487831_tradex";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>