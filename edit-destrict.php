<?php
session_start();
require_once "./connection.php";

$user_type = isset($_SESSION["user_type"]) ? htmlspecialchars($_SESSION["user_type"]) : 'Unknown';

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

$district_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $district_name = mysqli_real_escape_string($conn, $_POST['district_name']);
    $state_id = mysqli_real_escape_string($conn, $_POST['state_id']);
    $district_status = isset($_POST['district_status']) ? 1 : 0;

    // Check for duplicate district name
    $check_sql = "SELECT * FROM district WHERE district_name='$district_name' AND state_id='$state_id' AND district_id != $district_id";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $message = "District name already exists in this state.";
    } else {
        // Prepare and execute the SQL statement to update the district
        $update_sql = "UPDATE district SET district_name='$district_name', state_id='$state_id', district_status=$district_status WHERE district_id=$district_id";
        if (mysqli_query($conn, $update_sql)) {
            header("Location: manage-destricts.php");
            exit();
        } else {
            $message = 'Error updating district: ' . mysqli_error($conn);
        }
    }
}

// Fetch district details for editing
$district_sql = "SELECT * FROM district WHERE district_id=$district_id";
$district_result = mysqli_query($conn, $district_sql);
$district = mysqli_fetch_assoc($district_result);

if (!$district) {
    die("District not found.");
}

// Fetch states for the dropdown
$states_sql = "SELECT * FROM state";
$states_result = mysqli_query($conn, $states_sql);

mysqli_close($conn);
?>
<?php include "./includes/header.php"  ?>
<link rel="stylesheet" href="./css/managestyles.css">
            <main>
                <div class="container-fluid px-4">
                    <h3 class="mt-4">Edit District</h3>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="./manage-destricts.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit District</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Edit District
                        </div>
                        <div class="card-body">
                            <?php if (!empty($message)) : ?>
                                <p class="message"><?php echo $message; ?></p>
                            <?php endif; ?>

                            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $district_id; ?>" onsubmit="return validateForm();">
                                <div class="form-group">
                                    <label for="district_name">District Name:</label>
                                    <input style="width: 50%;" type="text" class="form-control" id="district_name" name="district_name" value="<?php echo htmlspecialchars($district['district_name']); ?>" oninput="setupValidation('district_name', 'district_error', 'Please enter a district name.');">
                                    <span style="color: red;" class="message" id="district_error"></span>
                                </div>

                                <div class="form-group">
                                    <label for="state_id">State:</label><br>
                                    <div style="position: relative; display: inline-block; width: 50%;">
                                        <select style="width: 100%; -webkit-appearance: none; -moz-appearance: none; appearance: none; padding-right: 30px;" class="form-control" id="state_id" name="state_id" oninput="validateState();">
                                            <option value="">Select State</option>
                                            <?php
                                            while ($row = mysqli_fetch_assoc($states_result)) {
                                                $selected = $row['state_id'] == $district['state_id'] ? 'selected' : '';
                                                echo "<option value='" . $row['state_id'] . "' $selected>" . $row['state_name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: black; font-size: 0.75em;">&#9660;</span>
                                    </div>
                                    <span style="color: red;" class="message" id="state_error"></span>
                                </div>


                                <div class="form-group">
                                    <label for="district_status">Status:</label>
                                    <input type="checkbox" id="district_status" style="margin-top: 20px;" name="district_status" value="1" <?php echo isset($district['district_status']) && $district['district_status'] == 1 ? 'checked' : ''; ?>> Active
                                </div>

                                <button style="width: 140px; margin-left:390px;" type="submit" name="submit" class="btn btn-primary">Update District</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
           
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script src="js/scripts.js"></script>
    <script>
        function setupValidation(inputId, errorId, errorMessage) {
            var input = document.getElementById(inputId);
            var error = document.getElementById(errorId);

            input.addEventListener('input', function() {
                if (input.value.trim() === '') {
                    error.textContent = errorMessage;
                } else {
                    error.textContent = '';
                }
            });
        }

        function validateState() {
            var stateSelect = document.getElementById('state_id');
            var stateError = document.getElementById('state_error');
            if (stateSelect.value === '') {
                stateError.textContent = 'Please select a state.';
                return false;
            } else {
                stateError.textContent = '';
                return true;
            }
        }

        function validateForm() {
            var isValid = true;

            var districtInput = document.getElementById('district_name');
            var districtError = document.getElementById('district_error');

            if (districtInput.value.trim() === '') {
                districtError.textContent = 'Please enter a district name.';
                isValid = false;
            } else {
                districtError.textContent = '';
            }

            if (!validateState()) {
                isValid = false;
            }

            return isValid;
        }

        // Set up validation for district name
        setupValidation('district_name', 'district_error', 'Please enter a district name.');
    </script>
<?php include "./includes/footer.php"  ?>