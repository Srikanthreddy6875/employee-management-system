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
        <h3 class="mt-4">Designation List</h3>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="./dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Designations</li>
        </ol>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1" style="margin-top: 10px;"></i>
                Designations
                <button style="float: right;" class="btn btn-primary">
                    <a class="text-white text-decoration-none" href="./add-designation.php">Add Designation</a>
                </button>
            </div>
            <div class="card-body">
                <?php if ($message) : ?>
                    <div class="alert alert-info">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <div class='text-center pb-2'>
                </div>
                <table id="datatablesSimple" class="table table-hover">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th>S.No.</th>
                            <th>Designation Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM designation";
                        $result = mysqli_query($conn, $sql);

                        $i = 1;
                        if (mysqli_num_rows($result) > 0) {
                            while ($rows = mysqli_fetch_assoc($result)) {
                                $designation_name = htmlspecialchars($rows["designation_name"]);
                                $designation_status = $rows["designation_status"] ? 'Active' : 'Inactive';
                        ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $designation_name; ?></td>
                                    <td><?php echo $designation_status; ?></td>
                                    <td>
                                        <a class="btn btn-warning" href="./edit-designation.php?id=<?php echo $rows['designation_id']; ?>">Edit</a>
                                        <?php if ($_SESSION["user_type"] == "Super Admin") : ?>
                                            <a class="btn btn-danger" href="./delete-designation.php?id=<?php echo $rows['designation_id']; ?>" onclick="return confirm('Are you sure you want to delete this designation?');">Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='4'>No records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
<script src="js/datatables-simple-demo.js"></script>
<script src="./js/scripts.js"></script>
<?php include "./includes/footer.php" ?>