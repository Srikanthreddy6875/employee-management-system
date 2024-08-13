<?php

require_once "./connection.php";

session_start();

$id = intval($_GET["id"]);

if ($id <= 0) {
    die("Invalid ID");
}

$sql = "SELECT * FROM employee WHERE designation_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Designation is in use, cannot delete
    $_SESSION['message'] = "Designation cannot be deleted because it is used by one or more employees.";
} else {
    // Designation is not in use, proceed with deletion
    $sql = "DELETE FROM designation WHERE designation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Successfully deleted
        $_SESSION['message'] = "Designation deleted successfully.";
    } else {
        // Error occurred
        $_SESSION['message'] = "Error deleting record: " . $conn->error;
    }
}

$stmt->close();
$conn->close();

header("Location: manage-designation.php");
exit();
?>
