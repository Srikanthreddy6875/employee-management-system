<?php
session_start();
require_once "./connection.php";

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}
?>
<?php include "./includes/header.php" ?>
<main>
    <div class="container-fluid px-4">
        <h3 class="mt-4">Employees </h3>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Employees</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1" style="margin-top: 10px;"></i>
                Employees List
                <button style="float: right;" class="btn btn-primary"><a class="text-white text-decoration-none" href="./add-employee.php">Add Employee</a></button>
                <button style="float: right; margin-right: 10px;" class="btn btn-primary">
                    <a class="text-white text-decoration-none" href="./employee_list_dowenload.php">Download</a>
                </button>

            </div>

            <div class="card-body">
                <div class='text-center pb-2'>
                </div>
                <table id="datatablesSimple" class="table table-hover">
                    <thead>
                        <tr>
                            <th>EmpId</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>State</th>
                            <th>District</th>
                            <th>Profile Picture</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch employee data
                        $sql = "SELECT em.id, em.name, em.email,em.phone_number, d.designation_name, s.state_name, dist.district_name, em.profile_picture, em.employee_status 
                                            FROM employee AS em 
                                            INNER JOIN designation AS d ON em.designation_id = d.designation_id
                                            INNER JOIN state AS s ON em.state_id = s.state_id
                                            INNER JOIN district AS dist ON em.district_id = dist.district_id";
                        $result = mysqli_query($conn, $sql);

                        if ($result) {
                            if (mysqli_num_rows($result) > 0) {
                                while ($rows = mysqli_fetch_assoc($result)) {
                                    $id = htmlspecialchars($rows["id"]);
                                    $name = htmlspecialchars($rows["name"]);
                                    $email = htmlspecialchars($rows["email"]);
                                    $designation = htmlspecialchars($rows["designation_name"]);
                                    $state = htmlspecialchars($rows["state_name"]);
                                    $district = htmlspecialchars($rows["district_name"]);
                                    $phone = htmlspecialchars($rows["phone_number"]);
                                    $empprofile = htmlspecialchars($rows["profile_picture"]);
                                    $emp_status = $rows["employee_status"] ? 'Active' : 'Deactive';

                                    $formatted_id = sprintf('EMP%03d', $id);
                                    $profile_picture_url = $empprofile ? 'emp_profile_upload/' . $empprofile : './path/to/default-image.jpg';
                        ?>
                                    <tr>
                                        <td><?php echo $formatted_id; ?></td>
                                        <td><?php echo $name; ?></td>
                                        <td><?php echo $designation; ?></td>
                                        <td><?php echo $state; ?></td>
                                        <td><?php echo $district; ?></td>
                                        <td><img src="<?php echo $profile_picture_url; ?>" alt="Profile Picture" style="width: 50px; height: 50px;"></td>
                                        <td><?php echo $email; ?></td>
                                        <td><?php echo $phone; ?></td>
                                        <td><?php echo $emp_status; ?></td>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-center " style="gap: 4px;">
                                                <a class="btn btn-warning" href="./edit-employee.php?id=<?php echo $id; ?>">edit</a>
                                                <a class="btn btn-danger delete-button" href="./delete-employee.php?id=<?php echo $id; ?>" onclick="confirmDelete(event)">delete</a>
                                            </div>
                                        </td>
                                    </tr>
                        <?php
                                }
                            } else {
                                echo '<tr><td colspan="9" class="text-center">No Reacords Found</td></tr>';
                            }
                        } else {
                            echo "Error executing query: " . mysqli_error($conn);
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="js/datatables-simple-demo.js"></script>
<script>
    function confirmDelete(event) {
        event.preventDefault();
        if (confirm("Are you sure you want to delete this record?")) {
            window.location.href = event.target.href;
        }
    }
</script>
<?php include "./includes/footer.php" ?>