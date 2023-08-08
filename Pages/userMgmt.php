<!DOCTYPE html>
<html lang="en">
<head>
    <title>Key inventory</title>
    <link rel="stylesheet" type="text/css" href="../CSS.css">
    <meta charset="UTF-8" name="author" content="Zadworny Tom">
</head>
<h1>User management</h1>
<div class="navigation">
    <a href="../index.php">Homepage</a>
    <a href="keyMgmt.php" style="margin-left: 3%; margin-right: 1.5%">Key management</a>
    <a href="userMgmt.php" style="margin-left: 1.5%; margin-right: 3%"><b>User management</b></a>
    <a href="lockerMgmt.php">Locker management</a>
</div>
<br><br>

<!-- Add user -->
<form action="userMgmt.php" method="post">
    <input required type="text" name="username" placeholder="Full name (Name Family-name)">
    <input required type="submit" name="add_user" value="Add user">
</form>


<?php
//Connect to the database
include_once("credentials.php");
global $connect;

//Add key
if (isset($_POST["add_user"])) {
    //Define variables
    $username = $_POST["username"];

    //Check if the input is empty
    if (!$username == "") {
        //Check if the user already exists in the database
           $select = $connect->prepare("SELECT * FROM users WHERE full_name = ?");
           $select->bind_param("s", $username);
           $select->execute();
           $result = $select->get_result();
           if ($result->num_rows > 0) {
               echo ("<p style='color: red;'>User already exists</p>");
               header("refresh: 3");
               return;
           }

           //If everything is fine, add the key
           else {
               $insert = $connect->prepare("INSERT INTO users (full_name) VALUES (?)");
               $insert->bind_param("s", $username);
               $insert->execute();
               echo ("<p style='color: green;'>User added</p>");
               header("refresh: 3");
           }
    }

    else {
        echo ("<p style='color: red;'>The username cannot be empty.</p>");
        header("refresh: 3");
        return;
    }
}

//Print database
//Keys
$select =  $connect->prepare("SELECT * FROM users order by full_name");
$select->execute();
$result = $select->get_result();

echo ("<div class='data'>");
echo ("<h3>Username</h3>");
while ($row = $result->fetch_assoc()) {
    echo ("<p id='keys'>" . $row["full_name"] . "</p>");
}