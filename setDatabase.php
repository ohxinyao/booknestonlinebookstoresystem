<?php
$host = 'localhost';
$dbname = 'booknest_database';
$username = 'root';
$password = '';

try 
{
    //create connection object
    $pdo = new PDO("mysql:host=$host; 
                    dbname=$dbname; 
                    charset=utf8", $username, $password);
    //set error mode to exception (help while debugging)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 

// Show error message if connection fails
catch(PDOException $e) 
{
    die("Connection failed: " . $e->getMessage());
}
?>