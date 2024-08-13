<?php
session_start();
require_once "./connection.php";

$id =  $_GET["id"];
$sql = "DELETE FROM state WHERE state_id='$id'";

mysqli_query($conn , $sql); 

header("Location: manage-state.php" );

?>
