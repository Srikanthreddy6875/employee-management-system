<?php
session_start();
require_once "./connection.php";

// Functions for database operations
function escapeData($conn, $data) {
    return $conn->real_escape_string($data);
}

function isDesignationExists($conn, $designation_name, $designation_id) {
    $designation_name = escapeData($conn, $designation_name);
    $designation_id = escapeData($conn, $designation_id);

    $sql = "SELECT * FROM Designation WHERE designation_name='$designation_name' AND designation_id != '$designation_id'";
    $result = $conn->query($sql);

    return $result->num_rows > 0;
}

function updateDesignation($conn, $designation_id, $designation_name, $designation_status) {
    $designation_id = escapeData($conn, $designation_id);
    $designation_name = escapeData($conn, $designation_name);
    $designation_status = escapeData($conn, $designation_status);

    $sql = "UPDATE Designation SET designation_name='$designation_name', designation_status='$designation_status' WHERE designation_id='$designation_id'";

    return $conn->query($sql);
}

$user_type = isset($_SESSION["user_type"]) ? htmlspecialchars($_SESSION["user_type"]) : 'Unknown';

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

$designation_name = '';
$designation_status = '';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $designation_id = $_POST['designation_id'];
    $designation_name = $_POST['designation_name'];
    $designation_status = isset($_POST['designation_status']) ? 1 : 0;

    if (isDesignationExists($conn, $designation_name, $designation_id)) {
        $message = "Designation name already exists.";
    } else {
        if (updateDesignation($conn, $designation_id, $designation_name, $designation_status)) {
            header("Location: manage-designation.php");
            exit();
        } else {
            $message = "Error updating record: " . $conn->error;
        }
    }
}

if (isset($_GET['id'])) {
    $designation_id = $_GET['id'];
    $designation_id = $conn->real_escape_string($designation_id);

    $sql = "SELECT * FROM Designation WHERE designation_id='$designation_id'";
    $result_designation = $conn->query($sql);

    if ($result_designation->num_rows == 1) {
        $row = $result_designation->fetch_assoc();
        $designation_name = $row['designation_name'];
        $designation_status = $row['designation_status'];
    } else {
        $message = "Designation not found";
    }
}

$conn->close();
?>
<script>
    function validateForm() {
        let designationName = document.getElementById('designation_name').value.trim();
        let error = '';

        if (designationName === '') {
            error = 'Please enter a valid designation name.';
        }

        document.getElementById('designation_error').innerText = error;
        return error === '';
    }
</script>
<?php include "./includes/header.php" ?>
<link rel="stylesheet" href="./css/managestyles.css">
<main>
    <div class="container-fluid px-4">
        <h3 class="mt-4">Edit Designation</h3>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="./manage-designation.php">Dashboard</a></li>
            <!-- <li class="breadcrumb-item"><a href="./manage-designation.php">Designations</a></li> -->
            <li class="breadcrumb-item active">Edit Designation</li>
        </ol>
        <div class="card mb-4" >
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Edit Designation
            </div>
            <div class="card-body">
                <?php if ($message != ''): ?>
                    <div class="alert alert-danger"><?php echo $message; ?></div>
                <?php endif; ?>
                <form style="width: 50%;" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm();">
                    <input type="hidden" name="designation_id" value="<?php echo htmlspecialchars($designation_id); ?>">
                    <div class="form-group">
                        <label for="designation_name">Designation Name</label>
                        <input type="text" class="form-control" id="designation_name" name="designation_name" value="<?php echo htmlspecialchars($designation_name); ?>" oninput="validateForm();">
                        <span style="margin: 20px;" class="message" id="designation_error"></span>
                    </div>

                    <div class="form-group">
                        <label for="designation_status"></label>
                        <input type="checkbox" id="designation_status" name="designation_status" value="1" <?php if ($designation_status == 1) echo 'checked'; ?>> Status
                    </div>

                    <button style="margin: 10px; width:140px; margin-left:350px;" type="submit" name="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</main>
<footer class="py-4 bg-light mt-auto">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Copyright &copy; Your Website 2024</div>
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
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
<script src="js/datatables-simple-demo.js"></script>
<script src="./js/scripts.js"></script>
<?php include "./includes/footer.php" ?>
