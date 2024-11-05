<?php
$host="localhost";
$dbname="local_db";
$username="root";
$password="";
//Create new connection to mySQL database localhost using username and password
$pdo = new PDO("mysql:host=$host;dbname=$dbname",$username,$password);
return $pdo;