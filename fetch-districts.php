<?php
require_once "./connection.php";

if (isset($_POST['state_id'])) {
    $state_id = intval($_POST['state_id']);
    
    $sql = "SELECT district_id, district_name FROM district WHERE state_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $state_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<li data-value=\"" . $row['district_id'] . "\">" . htmlspecialchars($row['district_name']) . "</li>";
        }
    } else {
        echo "<li data-value=\"\" class=\"disabled\">No districts available</li>";
    }
}
?>
