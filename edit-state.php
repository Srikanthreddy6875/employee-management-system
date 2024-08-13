<?php
session_start();
require_once "./connection.php";

$user_type = isset($_SESSION["user_type"]) ? htmlspecialchars($_SESSION["user_type"]) : 'Unknown';

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

$state_name = '';
$state_status = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $state_id = $_POST['state_id'];
    $state_name = $_POST['state_name'];
    $state_status = isset($_POST['state_status']) ? 1 : 0;

    // Check for duplicate state name
    $check_sql = "SELECT * FROM state WHERE state_name='$state_name' AND state_id != '$state_id'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "State already exists.";
    } else {
        $sql = "UPDATE state SET state_name='$state_name', state_status='$state_status' WHERE state_id='$state_id'";

        if (mysqli_query($conn, $sql)) {
            header("Location: manage-state.php");
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }
}

if (isset($_GET['id'])) {
    $state_id = $_GET['id'];

    $sql = "SELECT * FROM state WHERE state_id='$state_id'";
    $result_state = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result_state) == 1) {
        $row = mysqli_fetch_assoc($result_state);
        $state_name = $row['state_name'];
        $state_status = $row['state_status'];
    } else {
        echo "State not found";
    }
}

mysqli_close($conn);
?>
<?php include "./includes/header.php" ?>
            <main>
                <div class="container-fluid px-4">
                    <h3 class="mt-4">Edit State</h3>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="./manage-state.php">Dashboard</a></li>
                        <!-- <li class="breadcrumb-item"><a href="./manage-state.php">State</a></li> -->
                        <li class="breadcrumb-item active">Edit State</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-edit me-1"></i>
                            Edit State
                        </div>
                        <div class="card-body">
                            <?php if ($error_message): ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($error_message); ?>
                                </div>
                            <?php endif; ?>
                            <form style="width: 50%;" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateFormstate();">
                                <input type="hidden" name="state_id" value="<?php echo htmlspecialchars($state_id); ?>">
                                <div class="form-group">
                                    <label for="state_name">State Name</label>
                                    <input type="text" class="form-control" id="state_name" name="state_name" value="<?php echo htmlspecialchars($state_name); ?>"  oninput="setupValidation('state_name', 'state_error', 'Please enter a state name.');">
                                    <span style="color: red; margin:20px;" class="message" id="state_error"></span>
                                </div>

                                <div class="form-group">
                                    <label for="state_status">Status</label>
                                    <input type="checkbox" id="state_status" name="state_status" value="1" <?php if ($state_status == 1) echo 'checked'; ?>> Active
                                </div>

                                <button style="margin: 10px; margin-left:390px; width:140px;" type="submit" name="submit" class="btn btn-primary">Save Changes</button>
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
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script src="js/datatables-simple-demo.js"></script>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="./js/scripts.js"></script>
    <script>
        function setupValidation(inputId, errorId, errorMessage) {
            var inputField = document.getElementById(inputId);
            var errorField = document.getElementById(errorId);

            inputField.addEventListener('input', function() {
                if (inputField.value.trim() === '') {
                    errorField.textContent = errorMessage;
                } else {
                    errorField.textContent = '';
                }
            });
        }

        function validateFormstate() {
            var isValid = true;
            var stateName = document.getElementById('state_name');
            var stateError = document.getElementById('state_error');

            if (stateName.value.trim() === '') {
                stateError.textContent = 'Please enter a state name.';
                isValid = false;
            } else {
                stateError.textContent = '';
            }

            return isValid;
        }
    </script>
    <?php include "./includes/header.php" ?>