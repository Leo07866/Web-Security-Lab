<?php
// Connect to the database
$username = ""; // Put your database username in the quotations
$password = ""; // Put your database password in the quotations
$host = "";
$db = "foobar_users"; // In our case the database name is the same as the username (normally it is 
                // different) so we can set it as the same as the username

// Connect to the MySQL server and select the required database
$connection = mysqli_connect($host, $username, $password, $db);




