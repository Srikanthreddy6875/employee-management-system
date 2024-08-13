
<?php
session_start();
require_once "./connection.php";

$user_type = isset($_SESSION["user_type"]) ? htmlspecialchars($_SESSION["user_type"]) : 'Unknown';

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $district_name = mysqli_real_escape_string($conn, $_POST['district_name']);
    $state_id = mysqli_real_escape_string($conn, $_POST['state_id']);
    $district_status = isset($_POST['district_status']) ? 1 : 0;

    // Check for duplicate district name
    $check_sql = "SELECT * FROM district WHERE district_name='$district_name' AND state_id='$state_id'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $message = "District name already exists in this state.";
    } else {
        $insert_sql = "INSERT INTO district (district_name, state_id, district_status) VALUES ('$district_name', '$state_id', $district_status)";
        if (mysqli_query($conn, $insert_sql)) {
            header("Location: manage-destricts.php");
            exit();
        } else {
            $message = 'Error adding district: ' . mysqli_error($conn);
        }
    }
}

// Fetch states for the dropdown
$states_sql = "SELECT * FROM state";
$states_result = mysqli_query($conn, $states_sql);

mysqli_close($conn);
?>
<?php include "./includes/header.php" ?>
<link rel="stylesheet" href="./css/managestyles.css">
            <main>
                <div class="container-fluid px-4">
                    <h3 class="mt-4">Add District</h3>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="./manage-destricts.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Add District</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Add District
                        </div>
                        <div class="card-body">
                            <?php if (isset($message)) : ?>
                                <p class="message"><?php echo $message; ?></p>
                            <?php endif; ?>

                            <form style="width: 50%;" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm();">
                                <div class="form-group">
                                    <label for="district_name">District Name:</label>
                                    <input type="text" class="form-control" id="district_name" name="district_name" oninput="setupValidation('district_name', 'district_error', 'Please enter a district name.');">
                                    <span style="color: red;" class="message" id="district_error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="state_id">State:</label>
                                    <div style="position: relative; display: inline-block; width: 100%;">
                                        <select class="form-control" id="state_id" name="state_id" oninput="validateState();" style="width: 100%; -webkit-appearance: none; -moz-appearance: none; appearance: none; padding-right: 30px;">
                                            <option value="">Select State</option>
                                            <?php
                                            while ($row = mysqli_fetch_assoc($states_result)) {
                                                echo "<option value='" . $row['state_id'] . "'>" . $row['state_name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: black; font-size: 0.75em;">&#9660;</span>
                                    </div>
                                    <span style="color: red;" class="message" id="state_error"></span>
                                </div>

                                <div class="form-group" style="margin-top: 20px;">
                                    <label for="district_status"></label>
                                    <input type="checkbox" id="district_status" name="district_status" value="1" checked> Status
                                </div>
                                <button type="submit" style="margin-left: 390px; width:140px;" name="submit" class="btn btn-primary">Add District</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
            
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="./js/common.js"></script>
    <?php include "./includes/header.php" ?>