<!DOCTYPE html>
<html lang="en">
<head>
    <title>Key inventory</title>
    <link rel="stylesheet" type="text/css" href="CSS.css">
    <meta charset="UTF-8" name="author" content="Zadworny Tom">
</head>
<h1>Welcome to the key inventory</h1>
<div class="navigation">
    <a href="index.php"><b>Homepage</b></a>
    <a href="Pages/keyMgmt.php" style="margin-left: 3%; margin-right: 1.5%">Key management</a>
    <a href="Pages/userMgmt.php" style="margin-left: 1.5%; margin-right: 3%">User management</a>
    <a href="Pages/lockerMgmt.php">Locker management</a>
</div>
<br>
<?php
    //Connect to the MySQL database
    include_once("Pages/credentials.php");
    global $connect;

    echo ("<h3 style='display: flex; flex-direction: row; flex-wrap: wrap; justify-content: center; font-size: 25px'>Summary</h3>");
    echo ("<br>");
    //Display the assigned lockers
    $select = $connect->prepare("SELECT * FROM selection order by key_id");
    $select->execute();
    $result = $select->get_result();
?>

<table align = "center" border = "10" cellpadding = "10" cellspacing = "3" class="data">
    <tr>
        <td>Keys</td>
        <td>Name</td>
        <td>Locker</td>
        <td>Location</td>
        <td>Notes</td>
    </tr>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        if ($row["key_id"] == 0) {
            echo ("<td >" . "None" . "</td>");
        }
        else {
            echo ("<td >" . $row["key_id"] . "</td>");
        }
        echo ("<td >" . $row["full_name"] . "</td>");
        echo ("<td >" . $row["locker_id"] . "</td>");
        echo ("<td >" . $row["location"] . "</td>");
        echo ("<td >" . $row["notes"] . "</td>");
        echo "</tr>";
    }
    ?>
</table>

<footer>
    <p>Made by Ilovemyhouse
        <br>
    Version 1.0.2</p>
</footer>
</html>