<?php

$hostname = "localhost";
$database = "tracker";
$username = "root";
$password = "";

$connection = mysqli_connect($hostname, $username, $password, $database);

if($connection == true){
    echo '<script>window.alert("Connection successful!");</script>';
}

else{
    echo '<script>window.alert("Connection failed");</script>';
}



?>