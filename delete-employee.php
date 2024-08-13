<?php 

require_once "./connection.php";

$id =  $_GET["id"];

$sql = "DELETE FROM employee WHERE id = $id ";

mysqli_query($conn , $sql); 

header("Location: manage-employe.php" );


?>
