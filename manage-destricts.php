<?php
session_start();
require_once "./connection.php";

$user_type = isset($_SESSION["user_type"]) ? htmlspecialchars($_SESSION["user_type"]) : 'Unknown';

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>
<?php include "./includes/header.php" ?>
<main>
    <div class="container-fluid px-4">
        <h3 class="mt-4">Districts List</h3>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Districts</li>
        </ol>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1" style="margin-top: 10px;"></i>
                Districts
                <button style="float: right;" class="btn btn-primary"><a class="text-white text-decoration-none" href="./add-destricts.php">Add District</a></button>
            </div>
            <div class="card-body">
                <?php
                $district_sql = "SELECT dt.district_id,dt.district_name,dt.district_status,st.state_name FROM district as dt inner join state as st where st.state_id=dt.state_id";
                $district_result = mysqli_query($conn, $district_sql);

                if (!$district_result) {
                    echo "<p>Error executing query: " . mysqli_error($conn) . "</p>";
                }
                ?>
                <table id="datatablesSimple" class="table table-hover">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th>ID</th>
                            <th>State</th>
                            <th>District</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($district_result) > 0) {
                            while ($row = mysqli_fetch_assoc($district_result)) {
                                $district_id = htmlspecialchars($row['district_id']);
                                $state_name = htmlspecialchars($row['state_name']);
                                $district_name = htmlspecialchars($row['district_name']);
                                $district_status = htmlspecialchars($row['district_status']);
                                $status_text = ($district_status == 1) ? "Active" : "Inactive";
                        ?>
                                <tr>
                                    <td><?php echo $district_id; ?></td>
                                    <td><?php echo $state_name; ?></td>
                                    <td><?php echo $district_name; ?></td>
                                    <td><?php echo $status_text; ?></td>
                                    <td id="center-actions">
                                        <a href='./edit-destrict.php?id=<?php echo $district_id; ?>' class="btn btn-warning">Edit</a>
                                        <?php if ($user_type == "Admin" || $user_type == "Sub Admin" || $user_type == "Super Admin") : ?>
                                            <a href="javascript:void(0);" class="btn btn-danger" onclick="confirmDelete(<?php echo $district_id; ?>)">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='4'>No districts found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {
            searchable: true,
            fixedHeight: true,
            perPage: 5
        });
    });

    function confirmDelete(districtId) {
        if (confirm("Are you sure you want to delete this district?")) {
            window.location.href = `./delete-destricts.php?id=${districtId}`;
        }
    }
</script>
<?php include "./includes/footer.php" ?>