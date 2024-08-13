<?php
session_start();
require_once "./connection.php";

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

$name = $email = $pass = $pass_confirm = $designation = $state = $district = $profile_picture = $user_status = $phone_number = "";
$nameErr = $emailErr = $passErr = $passConfirmErr = $designationErr = $stateErr = $districtErr = $imageErr = $user_statusErr = $phoneNumberErr = "";

$uploadedImage = isset($_FILES['image']) ? $_FILES['image'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_changes'])) {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $pass = htmlspecialchars($_POST["pass"]);
    $pass_confirm = htmlspecialchars($_POST["pass_confirm"]);
    $designation = htmlspecialchars($_POST["designation"]);
    $state = htmlspecialchars($_POST["state"]);
    $district = htmlspecialchars($_POST["district"]);
    $user_status = isset($_POST['user_status']) ? 1 : 0;
    $phone_number = htmlspecialchars($_POST["phone_number"]);

    // Validate and handle file upload
    if ($uploadedImage && $uploadedImage['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "emp_profile_upload/";
        $imageName = basename($uploadedImage['name']);
        $imageName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $imageName); // Sanitize filename
        $targetPath = $uploadDir . $imageName;

        if (move_uploaded_file($uploadedImage['tmp_name'], $targetPath)) {
            $profile_picture = $imageName;
        } else {
            $imageErr = "Failed to upload image.";
        }
    } else {
        $imageErr = "Image is required.";
    }

    // Validation code
    if (empty($name)) {
        $nameErr = "* Name is required";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $nameErr = "* Name should contain only letters";
    }

    if (empty($email)) {
        $emailErr = "* Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "* Invalid email format";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM employee WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_check_email = $stmt->get_result();
        if ($result_check_email->num_rows > 0) {
            $emailErr = "Email already exists. Please use a different email.";
        }
        $stmt->close();
    }

    if (empty($pass)) {
        $passErr = "* Password is required";
    } elseif (!preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}/", $pass)) {
        $passErr = "* Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character";
    } elseif ($pass !== $pass_confirm) {
        $passConfirmErr = "* Passwords do not match";
    }

    if (empty($pass_confirm)) {
        $passErr = "* Password is required";
    } elseif ($pass_confirm !== $pass) {
        $passConfirmErr = "* Passwords do not match";
    }

    if ($designation == "Select Designation") {
        $designationErr = "* Please select a Designation";
    }

    if (empty($state) || $state == "Select State") {
        $stateErr = "* Please select a State";
    }

    if (empty($district) || $district == "Select District") {
        $districtErr = "* Please select a District";
    }

    if (empty($phone_number)) {
        $phoneNumberErr = "* Phone number is required";
    } elseif (!preg_match("/^[0-9]{10,15}$/", $phone_number)) {
        $phoneNumberErr = "* Phone number must be between 10 and 15 digits";
    }

    if (empty($nameErr) && empty($emailErr) && empty($passErr) && empty($passConfirmErr) && empty($designationErr) && empty($stateErr) && empty($districtErr) && empty($imageErr) && empty($phoneNumberErr)) {
        $stmt = $conn->prepare("INSERT INTO employee (name, email, password, designation_id, state_id, district_id, profile_picture, employee_status, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssssssi", $name, $email, $pass, $designation, $state, $district, $profile_picture, $user_status, $phone_number);
        if ($stmt->execute()) {
            header("Location: manage-employe.php");
            exit();
        } else {
            echo "<span style='color:red'>Error: " . $stmt->error . "</span>";
        }
        $stmt->close();
    }
}
?>

<?php include "./includes/header.php" ?>
<main>
    <link rel="stylesheet" href="./css/managestyles.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="./css/add-admin.css">
    <link rel="stylesheet" href="./css/add-emp-styles.css">
    <div class="container-fluid px-4">
        <h4 class="mt-4">Add New Employee</h4>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="./manage-employe.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Add Employee</li>
        </ol>
        <div class="card mb-4" style="margin-top: 20px;">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Add New Employee
            </div>
            <div class="card-body">
                <div id="addemp-div">
                    <div class="col-xl-6">
                        <form class="bodycard" style="margin-top: -50px;" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="return validateForm()" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Full Name :</label>
                                <input type="text" class="form-control" placeholder="Enter full name" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" oninput="validateName('name', 'nameErr')">
                                <span id="nameErr"><?php echo $nameErr; ?></span>
                            </div>

                            <div class="form-group">
                                <label>Email :</label>
                                <input type="email" class="form-control" placeholder="Enter email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" oninput="validateEmail('email', 'emailErr')">
                                <span id="emailErr"><?php echo $emailErr; ?></span>
                            </div>

                            <div class="form-group">
                                <label>Password :</label>
                                <input type="password" class="form-control" placeholder="Enter password" name="pass" id="pass" oninput="validatePassword('pass','passErr')">
                                <span id="passErr"><?php echo $passErr; ?></span>
                            </div>

                            <div class="form-group">
                                <label>Confirm Password :</label>
                                <input type="password" class="form-control" placeholder="Enter confirm password" name="pass_confirm" id="pass_confirm" oninput="validateConfirmPassword('pass_confirm','passConfirmErr')">
                                <span id="passConfirmErr"><?php echo $passConfirmErr; ?></span>
                            </div>

                            <div class="form-group">
                                <label>Designation :</label>
                                <div style="position: relative; display: inline-block; width: 100%;">
                                    <select class="form-control" name="designation" id="designation" oninput="validateDesignation('designation','designationErr')" style="width: 100%; -webkit-appearance: none; -moz-appearance: none; appearance: none; padding-right: 30px;">
                                        <option disabled> Select Designation</option>
                                        <?php
                                        $desQuery = $conn->query("SELECT designation_id, designation_name FROM designation WHERE designation_status = 1");
                                        while ($row = $desQuery->fetch_assoc()) {
                                            echo "<option value='" . $row['designation_id'] . "' " . ($row['designation_id'] == $designation ? "selected" : "") . ">" . $row['designation_name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: black;">&#9660;</span>
                                </div>
                                <span id="designationErr" style="color: red;"><?php echo $designationErr; ?></span>
                            </div>
                            <div class="form-group" style="margin-top: 6px;">
                                <label for="state">State:</label>
                                <div style="position: relative; display: inline-block; width: 100%;">
                                    <!-- Custom dropdown for states -->
                                    <div class="custom-dropdown">
                                        <div class="selected-state" id="selectedState" disabled>Select State</div>
                                        <div class="dropdown-content" id="stateDropdownContent">
                                            <!-- Search input inside the dropdown -->
                                            <input type="text" id="stateSearch" placeholder="Search for states..." onkeyup="filterStates()">
                                            <ul class="dropdown-list" id="stateList">
                                                <li data-value="" class="disabled">Select State</li>
                                                <?php
                                                $stateQuery = $conn->query("SELECT state_id, state_name FROM state");
                                                while ($row = $stateQuery->fetch_assoc()) {
                                                    echo "<li data-value='" . $row['state_id'] . "'>" . $row['state_name'] . "</li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: black;">&#9660;</span>
                                    </div>
                                    <input type="hidden" name="state" id="state">
                                    <span id="stateErr" style="color: red;"><?php echo $stateErr; ?></span>
                                </div>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const stateList = document.getElementById('stateList');
                                    const stateDropdownContent = document.getElementById('stateDropdownContent');
                                    const selectedState = document.getElementById('selectedState');
                                    const stateInput = document.getElementById('state');

                                    stateList.addEventListener('click', function(event) {
                                        const target = event.target;
                                        if (target.tagName === 'LI' && !target.classList.contains('disabled')) {
                                            const value = target.getAttribute('data-value');
                                            const text = target.textContent;

                                            selectedState.textContent = text;
                                            stateInput.value = value;
                                            stateDropdownContent.classList.remove('show');
                                        }
                                    });

                                    selectedState.addEventListener('click', function() {
                                        stateDropdownContent.classList.toggle('show');
                                    });

                                    document.addEventListener('click', function(event) {
                                        if (!stateDropdownContent.contains(event.target) && !selectedState.contains(event.target)) {
                                            stateDropdownContent.classList.remove('show');
                                        }
                                    });
                                });

                                function filterStates() {
                                    const input = document.getElementById('stateSearch');
                                    const filter = input.value.toLowerCase();
                                    const stateList = document.getElementById('stateList');
                                    const items = stateList.getElementsByTagName('li');

                                    for (let i = 0; i < items.length; i++) {
                                        const text = items[i].textContent || items[i].innerText;
                                        if (text.toLowerCase().indexOf(filter) > -1) {
                                            items[i].style.display = "";
                                        } else {
                                            items[i].style.display = "none";
                                        }
                                    }
                                }
                            </script>

                            <div class="form-group" style="margin-top: 6px;">
                                <label for="district">District:</label>
                                <div style="position: relative; display: inline-block; width: 100%;">
                                    <!-- Custom dropdown for districts -->
                                    <div class="custom-dropdown">
                                        <div class="selected-district" id="selectedDistrict">Select District</div>
                                        <div class="dropdown-content" id="districtDropdownContent">
                                            <!-- Search input inside the dropdown -->
                                            <input type="text" id="districtSearch" placeholder="Search for districts..." onkeyup="filterDistricts()">
                                            <ul class="dropdown-list" id="districtList">
                                                <li data-value="" class="disabled">Select State first</li>
                                            </ul>
                                        </div>
                                        <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: black;">&#9660;</span>
                                    </div>
                                    <input type="hidden" name="district" id="district">
                                    <span id="districtErr" style="color: red;"><?php echo $districtErr; ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Phone Number :</label>
                                <input type="number" class="form-control" placeholder="Enter phone number" name="phone_number" id="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" oninput="validatePhoneNumber('phone_number', 'phoneNumberErr')">
                                <span id="phoneNumberErr" class="error"><?php echo $phoneNumberErr; ?></span>
                            </div>


                            <div class="form-group">
                                <label>Profile Picture :</label>
                                <input type="file" class="form-control" name="image" id="image">
                                <span id="imageErr"><?php echo $imageErr; ?></span>
                            </div>

                            <div class="form-group">
                                <label for="user_status"></label><br>
                                <input type="checkbox" name="user_status" id="user_status" checked> Status
                            </div>
                            <div class="form-group">

                            </div>
                            <button id="addem_btn" style="width: 140px;margin-left: 180px;" type="submit" name="save_changes" class="btn btn-primary">Add Employee</button>
                        </form>

                    </div>
                </div>
            </div>
</main>
<script src="./js/state-desyricts-filters.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="./js/common.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    // $(document).ready(function() {
    //     $('#state').change(function() {
    //         var stateID = $(this).val();
    //         if (stateID) {
    //             $.ajax({
    //                 type: 'POST',
    //                 url: 'get_districts.php',
    //                 data: {
    //                     state_id: stateID
    //                 },
    //                 success: function(data) {
    //                     $('#district').html(data);
    //                 },
    //                 error: function(xhr, status, error) {
    //                     console.error('AJAX Error:', status, error);
    //                     $('#district').html('<option value="">Error loading districts</option>');
    //                 }
    //             });
    //         } else {
    //             $('#district').html('<option value="">Select State first</option>');
    //         }
    //     });
    // });

    // $(document).ready(function() {
    //     // Initialize Select2 on the state dropdown
    //     $('#state').select2({
    //         placeholder: 'Select State',
    //         allowClear: true
    //     });

    //     // Update placeholder color if needed
    //     $('#state').on('select2:open', function() {
    //         $('.select2-container--default .select2-selection--single .select2-selection__placeholder').css('color', '#999');
    //     });
    // });
</script>
<?php include "./includes/footer.php" ?>