<?php
session_start();
require_once "./connection.php";

$user_type = isset($_SESSION["user_type"]) ? htmlspecialchars($_SESSION["user_type"]) : 'Unknown';

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $designation_name = trim($conn->real_escape_string($_POST['designation_name']));
    $designation_status = isset($_POST['designation_status']) ? 1 : 0;

    if (empty($name)) {
        $nameErr = "* Name is required";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $nameErr = "* Name should contain only letters";
    }

    if (empty($designation_name)) {
        $message = "Please enter a designation name.";
    } else {
        // Check for duplicate designation name
        $sql = "SELECT * FROM Designation WHERE designation_name='$designation_name'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $message = "Designation name already exists.";
        } else {
            $sql = "INSERT INTO Designation (designation_name, designation_status) VALUES ('$designation_name', '$designation_status')";
            if ($conn->query($sql) === TRUE) {
                header("Location: manage-designation.php");
                exit();
            } else {
                $message = "Error creating designation: " . $conn->error;
            }
        }
    }
}
?>
<?php include "./includes/header.php" ?>
<link rel="stylesheet" href="./css/managestyles.css">
            <main>
                <div class="container-fluid px-4">
                    <h4 class="mt-4">Add Designation</h4>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="./manage-designation.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Designation</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Add Designation
                        </div>
                        <div class="card-body">
                            <form style="width: 50%;" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                <div class="form-group">
                                    <label for="designation_name">Designation Name</label>
                                    <input type="text" name="designation_name" id="designation_name" class="form-control" oninput="  validateName('name', 'nameErr'); setupValidation('designation_name', 'designation_error', 'Please enter a designation name.');" />
                                    <span style="margin: 20px;" class="message" id="designation_error"><?php echo $message; ?></span>
                                </div>
                                <div class="form-group">
                                    <label for="designation_status"></label>
                                    <input type="checkbox" name="designation_status" id="designation_status" value="1" checked /> Status
                                </div>
                                <button style="margin: 10px; margin-left: 380px; width:140px" type="submit" name="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="./js/scripts.js"></script>
   <script src="./js/common.js"></script>
   <?php include "./includes/header.php" ?>