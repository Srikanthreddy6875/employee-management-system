<?php
session_start();
require_once "./connection.php";

$user_type = isset($_SESSION["user_type"]) ? htmlspecialchars($_SESSION["user_type"]) : 'Unknown';

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}
?>
<?php include "./includes/header.php" ?>
            <main>
                <div class="container-fluid px-4">
                    <h3 class="mt-4">Users</h3>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <span><i class="fas fa-table me-1" style="margin-top: 10px;"></i>
                                Users List</span>
                            <button style="float: right;" class="btn btn-primary"><a class="text-white text-decoration-none" href="./add-admin.php">Add User</a></button>
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-hover">
                                <thead>
                                    <tr class="bg-dark text-white ">
                                        <th>S.No.</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Date of Birth</th>
                                        <th>Status</th>
                                        <th>User Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch admin data
                                    $sql = "SELECT * FROM admin";
                                    $result = mysqli_query($conn, $sql);
                                    $i = 1;
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($rows = mysqli_fetch_assoc($result)) {
                                            $name = htmlspecialchars($rows["name"]);
                                            $email = htmlspecialchars($rows["email"]);
                                            $dob = htmlspecialchars($rows["dob"]);
                                            $status = $rows["admin_status"] ? 'Active' : 'Inactive';
                                            $user_type = htmlspecialchars($rows["user_type"]);
                                            $id = $rows["id"];
                                    ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo $name; ?></td>
                                                <td><?php echo $email; ?></td>
                                                <td><?php echo $dob; ?></td>
                                                <td><?php echo $status; ?></td>
                                                <td><?php echo $user_type; ?></td>
                                                <td>
                                                    <a class="btn btn-warning" href="./edit-admin.php?id=<?php echo $id; ?>">Edit</a>
                                                    <?php if ($_SESSION["user_type"] == "Super Admin" || $_SESSION["user_type"] == "Admin" || $_SESSION["user_type"] == "Sub Admin") : ?>
                                                        <a class="btn btn-danger delete-button" href="./delete-admin.php?id=<?php echo $id; ?>">Delete</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>No admins found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms & Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
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

            document.querySelectorAll('.delete-button').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    if (!confirm('Are you sure you want to delete this admin?')) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
<?php include "./includes/footer.php" ?>