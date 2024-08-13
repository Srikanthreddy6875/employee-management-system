<?php
session_start();
require_once "./connection.php";

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

// Set headers to download file rather than display it
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=employee_list.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Output the column headings
fputcsv($output, array('EmpId', 'Name', 'Designation', 'State', 'District', 'Profile Picture', 'Email', 'Phone Number', 'Status'));

$sql = "SELECT em.id, em.name, em.email, em.phone_number, d.designation_name, s.state_name, dist.district_name, em.profile_picture, em.employee_status 
        FROM employee AS em 
        INNER JOIN designation AS d ON em.designation_id = d.designation_id
        INNER JOIN state AS s ON em.state_id = s.state_id
        INNER JOIN district AS dist ON em.district_id = dist.district_id";
$result = mysqli_query($conn, $sql);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        while ($rows = mysqli_fetch_assoc($result)) {
            $id = sprintf('EMP%03d', $rows["id"]);
            $name = $rows["name"];
            $designation = $rows["designation_name"];
            $state = $rows["state_name"];
            $district = $rows["district_name"];
            $profile_picture = $rows["profile_picture"];
            $email = $rows["email"];
            $phone = $rows["phone_number"];
            $emp_status = $rows["employee_status"] ? 'Active' : 'Deactive';

            // Write each row to the CSV
            fputcsv($output, array($id, $name, $designation, $state, $district, $profile_picture, $email, $phone, $emp_status));
        }
    }
} else {
    echo "Error executing query: " . mysqli_error($conn);
}

// Close the output stream
fclose($output);
exit();
?>
