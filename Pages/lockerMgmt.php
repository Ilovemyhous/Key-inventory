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
    <a href="keyMgmt.php" style="margin-left: 3%; margin-right: 1.5%">Key management</a>
    <a href="userMgmt.php" style="margin-left: 1.5%; margin-right: 3%">User management</a>
    <a href="lockerMgmt.php"><b>Locker management</b></a>
</div>
<br><br>

<!-- Add locker -->
<h3>Add locker</h3>
<form action="lockerMgmt.php" method="post">
    <input required type="text" name="locker_id" placeholder="Locker ID">
    <input required type="text" name="location" placeholder="Location">
    <input type="text" name="notes" placeholder="Notes">
    <input type="submit" name="add_locker" value="Add locker">
</form>

<?php
//Connect to the database
include_once("credentials.php");
global $connect;

//Add key
if (isset($_POST["add_locker"])) {
    //Define variables
    $locker_id = $_POST["locker_id"];
    $location = $_POST["location"];
    $notes = $_POST["notes"];

    //Check if the input is empty
    if (!$locker_id == "" || !$location == "") {
        //Now check if the input is negative
        //Finally, check if the key already exists in the database
        $select = $connect->prepare("SELECT * FROM lockers WHERE locker_id = ?");
        $select->bind_param("s", $locker_id);
        $select->execute();
        $result = $select->get_result();
        if ($result->num_rows > 0) {
            echo ("<p style='color: red;'>Locker already exists</p>");
            header("Refresh:3");
            return;
        }

        //If everything is fine, add the key
        else {
            $insert = $connect->prepare("INSERT INTO lockers (locker_id, location, notes) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $locker_id, $location, $notes);
            $insert->execute();
            echo ("<p style='color: green;'>Locker added</p>");
            header("Refresh:3");
        }

    }
    else {
        echo ("<p style='color: red;'>Locker ID or the location are empty</p>");
        header("Refresh:5");
        return;
    }
}

//Print database
//Keys
$select =  $connect->prepare("SELECT * FROM lockers");
$select->execute();
$result = $select->get_result();

echo ("<div class='data'>");
echo ("<h3>Lockers</h3>");
echo ("<br>");
echo ("<p>Locker ID | Location | Notes</p>");
?>
<!-- Manage lockers -->
<table align = "center" border = "10" cellpadding = "10" cellspacing = "3" class="data">
    <tr>
        <td>Lockers</td>
        <td>Location</td>
        <td>Notes</td>
    </tr>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo ("<td >" . $row["locker_id"] . "</td>");
        echo ("<td >" . $row["location"] . "</td>");
        echo ("<td >" . $row["notes"] . "</td>");
        echo "</tr>";
    }
    echo ("<br>");
    ?>
</table>

<?php
    //Get the users from the database
    $select =  $connect->prepare("SELECT full_name FROM users order by full_name");
    $select->execute();
    $result = $select->get_result();
?>
<br>

<form method="POST">
<br>
<h3>Manage lockers</h3>
<p>User</p>
<select name="username">
    <?php
    while ($row = $result->fetch_assoc()) {
        echo ("<option value='" . $row["full_name"] . "'>" . $row["full_name"] . "</option>");
    }
    ?>
</select>

<!-- Keys -->
<?php
    //Get the keys from the database
    $select =  $connect->prepare("SELECT key_id FROM `keys` order by key_id");
    $select->execute();
    $result = $select->get_result();
?>
<br><br>
<p>Key</p>
<select name="key">
    <?php
    while ($row = $result->fetch_assoc()) {
        echo ("<option value='" . $row["key_id"] . "'>" . $row["key_id"] . "</option>");
    }
    ?>
</select>
<br>
<i><p>Put the Key ID 0 if the locker doesn't need a key.</p></i>
<!-- Lockers -->
<?php
    //Get the lockers from the database
    $select =  $connect->prepare("SELECT locker_id FROM lockers order by locker_id");
    $select->execute();
    $result = $select->get_result();
?>
<br>
<p>Locker</p>
<select name="locker">
    <?php
    while ($row = $result->fetch_assoc()) {
        echo ("<option value='" . $row["locker_id"] . "'>" . $row["locker_id"] . "</option>");
    }
    ?>
</select>
<button type="submit" name="assign">Assign key and locker to user</button>
<br>
<br>
<br>
</form>

<?php
    //Assign key and locker to user
    if (isset($_POST["assign"])) {
        //Define variables
        $username = $_POST["username"];
        $key = $_POST["key"];
        $locker = $_POST["locker"];

        //Check if the input is empty
        if (!$username == "" || !$key == "" || !$locker == "") {
            //Now, check if the assigment already exists
            $select = $connect->prepare("SELECT * FROM selection WHERE full_name = ? AND key_id = ? AND locker_id = ?");
            $select->bind_param("sis", $username, $key, $locker);
            $select->execute();
            $result = $select->get_result();

            if (!$result->num_rows > 0) {

                //Are there still enough keys?
                $select = $connect->prepare("SELECT amount, inUse FROM `keys` WHERE key_id = ?");
                $select->bind_param("i", $key);
                $select->execute();
                $result = $select->get_result();
                $row = $result->fetch_assoc();

                $inUse = $row["inUse"];
                $amount = $row["amount"];

                if ($inUse >= $amount) {
                    echo ("<p style='color: red;'>All keys are already in use</p>");
                    header("Refresh:3");
                    return;
                }

                else {
                    //Now, first grab the location (and notes) from the locker database
                    $select = $connect->prepare("SELECT location, notes FROM lockers WHERE locker_id = ?");
                    $select->bind_param("s", $locker);
                    $select->execute();
                    $result = $select->get_result();
                    $row = $result->fetch_assoc();
                    $location = $row["location"];
                    $notes = $row["notes"];

                    //Now, add the assignment to the database
                    $insert = $connect->prepare("INSERT INTO selection (full_name, key_id, locker_id, location, notes) VALUES (?, ?, ?, ?, ?)");
                    $insert->bind_param("sisss", $username, $key, $locker, $location, $notes);
                    $insert->execute();
                    echo ("<p style='color: #08b808;'>Assigment added</p>");
                    header("Refresh:3");

                    //Finally, increment the inUse row in the `keys` database
                    $update = $connect->prepare("UPDATE `keys` SET inUse = inUse + 1 WHERE key_id = ?");
                    $update->bind_param("i", $key);
                    $update->execute();
                }
            }

            else {
                echo ("<p style='color: red;'>This assigment already exists</p>");
                header("Refresh:3");
                return;
            }
        }

        else {
            echo ("<p style='color: red;'>Username, key or locker are empty</p>");
            header("Refresh:3");
            return;
        }
    }
?>