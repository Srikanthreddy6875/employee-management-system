<?php
require_once "connection.php";

if (isset($_POST['state_id'])) {
    $stateID = $_POST['state_id'];
    $query = $conn->prepare("SELECT district_id, district_name FROM district WHERE state_id = ?");
    $query->bind_param("i", $stateID);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // echo "<li data-value=''>Select District</li>"; // Add prompt option
        while ($row = $result->fetch_assoc()) {
            echo "<li data-value='" . $row['district_id'] . "'>" . $row['district_name'] . "</li>";
        }
    } else {
        echo "<li data-value=''>No districts found</li>";
    }

    $query->close();
    $conn->close();
}
?>
