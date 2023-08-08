<!DOCTYPE html>
<html lang="en">
<head>
    <title>Key inventory</title>
    <link rel="stylesheet" type="text/css" href="../CSS.css">
    <meta charset="UTF-8" name="author" content="Zadworny Tom">
</head>
<h1>Key management</h1>
<div class="navigation">
    <a href="../index.php">Homepage</a>
    <a href="keyMgmt.php" style="margin-left: 3%; margin-right: 1.5%"><b>Key management</b></a>
    <a href="userMgmt.php" style="margin-left: 1.5%; margin-right: 3%">User management</a>
    <a href="lockerMgmt.php">Locker management</a>
</div>
<br><br>

<!-- Add key -->
<form action="keyMgmt.php" method="post">
    <input required type="number" name="key_id" placeholder="Key ID">
    <input required type="number" name="amount" placeholder="Amount">
    <input type="submit" name="add_key" value="Add key">
</form>


<?php
    //Connect to the database
    include_once("credentials.php");
    global $connect;

    //Add key
    if (isset($_POST["add_key"])) {
        //Define variables
        $key_id = $_POST["key_id"];
        $amount = $_POST["amount"];

        //Check if the input is empty
        if (!$key_id == "" || !$amount == "") {
            //Now check if the input is negative
            if ($key_id < 0 || $amount < 0) {
                echo ("<p style='color: red;'>Key ID or amount cannot be negative</p>");
                header("refresh: 3");
                return;
            }

            else {
                //Then, check if the key already exists in the database
                $select = $connect->prepare("SELECT * FROM `keys` WHERE `key_id` = ?");
                $select->bind_param("i", $key_id);
                $select->execute();
                $result = $select->get_result();

                if ($result->num_rows > 0) {
                    echo ("<p style='color: red;'>Key already exists</p>");
                    header("refresh: 3");
                    return;
                }

                //If everything is fine, add the key
                else {
                    $insert = $connect->prepare("INSERT INTO `keys` (`key_id`, `amount`) VALUES (?, ?)");
                    $insert->bind_param("ii", $key_id, $amount);
                    $insert->execute();
                    echo ("<p style='color: green;'>Key added</p>");
                    header("refresh: 3");
                }
            }
        }
        else {
            echo ("<p style='color: red;'>Key ID or amount are empty</p>");
            header("refresh: 3");
            return;
        }
    }

    //Print database
    //Keys
    $select =  $connect->prepare("SELECT * FROM `keys` LIMIT 1, 18446744073709551615");
    $select->execute();
    $result = $select->get_result();

    echo ("<div class='data'>");
    echo ("<b><p>Key ID | Amount (in use)</p></b>");
?>
<!-- Manage lockers -->
<table align = "center" border = "10" cellpadding = "10" cellspacing = "3" class="data">
    <tr>
        <td>Key ID</td>
        <td>Amount</td>
        <td>In use</td>
    </tr>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo ("<td >" . $row["key_id"] . "</td>");
        echo ("<td >" . $row["amount"] . "</td>");
        if ($row["inUse"] == $row["amount"]) {
            echo ("<td style='color: red;'>" . $row["inUse"] . "</td>");
        }
        else {
            echo ("<td >" . $row["inUse"] . "</td>");
        }
        echo "</tr>";
    }
    echo ("<br>");
    ?>
</table>