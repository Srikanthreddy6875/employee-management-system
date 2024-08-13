<?php
session_start();
require_once "./connection.php";

if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    $id = $_GET["id"];
    
    $stmt = $conn->prepare("DELETE FROM district WHERE district_id = ?");
    
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: manage-destricts.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    echo "Invalid ID.";
}

$conn->close();
?>
