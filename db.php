<?php
/* Connect to a MySQL database using driver invocation */
$dsn = 'mysql:dbname=inputs;host=127.0.0.1';
$user = 'root';
$password = '';

try 
{
    $connection = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

?>