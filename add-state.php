<?php
session_start();
require_once "./connection.php";

$user_type = isset($_SESSION["user_type"]) ? htmlspecialchars($_SESSION["user_type"]) : 'Unknown';

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}
?>
<?php
require_once "./connection.php";

$state_name = '';
$state_status = 1;
$error_message = '';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $state_name = $_POST['state_name'];
    $state_status = isset($_POST['state_status']) ? 1 : 0;

    $sql_check = "SELECT * FROM state WHERE state_name = '$state_name'";
    $result_check = mysqli_query($conn, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        $message = "State already exists.";
    } else {
        $sql_insert = "INSERT INTO state (state_name, state_status) VALUES ('$state_name', '$state_status')";
        if (mysqli_query($conn, $sql_insert)) {
            header("Location: manage-state.php");
            exit();
        } else {
            $error_message = "Error adding record: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>
<?php include "./includes/header.php" ?>
<link rel="stylesheet" href="./css/managestyles.css">
            <main>
                <div class="container-fluid px-4">
                    <h3 class="mt-4">Add State</h3>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="./manage-state.php">Dashboard</a></li>
                        <!-- <li class="breadcrumb-item"><a href="./manage-state.php">State</a></li> -->
                        <li class="breadcrumb-item active">Add State</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-plus me-1"></i>
                            Add State
                        </div>
                        <div class="card-body" >
                            <?php if (!empty($error_message)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>
                            <form style="width: 50%;" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateFormstate();">
                                <div class="form-group">
                                    <label for="state_name">State Name</label>
                                    <input type="text" class="form-control" id="state_name" name="state_name" value="<?php echo htmlspecialchars($state_name); ?>" oninput="setupValidation('state_name', 'state_error', 'Please enter a state name.');">
                                    <span  style="color: red; margin:20px;" class="message" id="state_error"><?php echo $message; ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="state_status"></label>
                                    <input type="checkbox" id="state_status" name="state_status" value="1" <?php if ($state_status == 1) echo 'checked'; ?>> Status
                                </div>

                                <button style="margin: 10px;width:100px; margin-left: 430px;" type="submit" name="submit" class="btn btn-primary">Add State</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="./js/common.js"></script>
    <?php include "./includes/header.php" ?>