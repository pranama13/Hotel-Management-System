<?php

$server = "localhost";
$username = "holidayinn";
$password = "";
$database = "holidayinn";

$conn = mysqli_connect($server,$username,$password,$database);

if(!$conn){
    die("<script>alert('connection Failed.')</script>");
}
?>