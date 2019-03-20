<?php
$servername = "localhost";
$username = "root";
$password = "pwdpwd";
$database = "myDB";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $database);

// Check connection
if (mysqli_connect_errno()) {
    die("<br>Connection failed: " . mysqli_connect_error());
}
//echo "<br>Connected successfully";

?> 