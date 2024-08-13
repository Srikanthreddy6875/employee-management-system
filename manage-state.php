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
                    <h3 class="mt-4">State List</h3>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">States</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"  style="margin-top: 10px;"></i>
                            States
                            <button style="float: right;" class="btn btn-primary"><a class="text-white text-decoration-none" href="./add-state.php">Add State</a></button>
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-hover">
                                <thead>
                                    <tr class="bg-dark text-white">
                                        <th>S.No.</th>
                                        <th>State Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM state";
                                    $result = mysqli_query($conn, $sql);

                                    $i = 1;
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($rows = mysqli_fetch_assoc($result)) {
                                            $state_name = htmlspecialchars($rows["state_name"]);
                                            $state_status = $rows["state_status"] ? 'Active' : 'Deactive';
                                            $state_id = $rows["state_id"];
                                    ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo $state_name; ?></td>
                                                <td><?php echo $state_status; ?></td>
                                                <td>
                                                    <a class="btn btn-warning" href="./edit-state.php?id=<?php echo $state_id; ?>">Edit</a>
                                                    <?php if ($_SESSION["user_type"] == "Super Admin") : ?>
                                                        <a href="javascript:void(0);" class="btn btn-danger" onclick="confirmDelete(<?php echo $state_id; ?>)">Delete</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='4'>No states found</td></tr>";
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

        function confirmDelete(state_id) {
            if (confirm("Are you sure you want to delete this district?")) {
                window.location.href = `./delete-state.php?id=${state_id}`;
            }
        }
    </script>
<?php include "./includes/footer.php" ?>