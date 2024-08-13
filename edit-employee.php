<?php
session_start();
require_once "./connection.php";

// Ensure user is logged in
if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}

// Ensure user type is set
$user_type = htmlspecialchars($_SESSION["user_type"]);
$id = $_GET['id'] ?? null;
if ($id === null) {
    echo "No employee ID provided.";
    exit;
}
// Initialize variables and error messages
$name = $email = $phone_number = $designation = $profile_picture = $emp_status = $state = $district = "";
$nameErr = $emailErr = $phoneErr = $designationErr = $imageErr = $emp_statusErr = $stateErr = $districtErr = "";
$successMsg = $errorMsg = "";

// Fetch employee data
$sql = "SELECT * FROM employee WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row["name"];
    $email = $row["email"];
    $phone_number = $row["phone_number"];
    $emp_status = $row["employee_status"];
    $designation = $row["designation_id"];
    $profile_picture = $row["profile_picture"];
    $state = $row["state_id"];
    $district = $row["district_id"];
} else {
    echo "Employee not found.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"] ?? $name;
    $newEmail = $_POST["email"] ?? $email;
    $newPhone = $_POST["phone_number"] ?? $phone_number;
    $emp_status = isset($_POST["employee_status"]) ? 1 : 0;
    $newDesignation = $_POST["designation"] ?? $designation;
    $newState = $_POST["state"] ?? $state;
    $newDistrict = $_POST["district"] ?? $district;
    $uploadedImage = $_FILES["profile_picture"];

    // Handle image upload
    if ($uploadedImage && $uploadedImage['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "emp_profile_upload/";

        if ($profile_picture && file_exists($uploadDir . $profile_picture)) {
            unlink($uploadDir . $profile_picture);
        }

        $imageName = basename($uploadedImage['name']);
        $targetPath = $uploadDir . $imageName;

        if (move_uploaded_file($uploadedImage['tmp_name'], $targetPath)) {
            $profile_picture = $imageName;
        } else {
            $imageErr = "Failed to upload image.";
        }
    }
    
    // Validation code
    if (empty($name)) {
        $nameErr = "* Name is required";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $nameErr = "* Name should contain only letters";
    }

    if (empty($newEmail)) {
        $emailErr = "* Email is required";
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "* Invalid email format";
    }

    if (empty($newPhone)) {
        $phoneErr = "* Phone number is required";
    } elseif (!preg_match("/^[0-9]{10,15}$/", $newPhone)) {
        $phoneErr = "* Phone number must be between 10 and 15 digits";
    }

    if ($newDesignation == "") {
        $designationErr = "* Please select a Designation";
    }

    if (empty($newState)) {
        $stateErr = "* Please select a State";
    }

    if (empty($newDistrict)) {
        $districtErr = "* Please select a District";
    }

    // Check for unique email
    if ($newEmail != $email) {
        $sql_select_query = "SELECT email FROM employee WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($sql_select_query);
        $stmt->bind_param("si", $newEmail, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $emailErr = "* Email already registered";
        } else {
            $email = $newEmail;
        }
    }

    // Check for errors before updating
    if (empty($nameErr) && empty($emailErr) && empty($phoneErr) && empty($designationErr) && empty($stateErr) && empty($districtErr) && empty($imageErr)) {
        $sql = "UPDATE employee SET name = ?, email = ?, phone_number = ?, designation_id = ?, state_id = ?, district_id = ?, profile_picture = ?, employee_status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssisssi", $name, $email, $newPhone, $newDesignation, $newState, $newDistrict, $profile_picture, $emp_status, $id);

        if ($stmt->execute()) {
            $successMsg = "Profile updated successfully.";
            header("Location: manage-employe.php");
            exit;
        } else {
            $errorMsg = "Error: " . $conn->error;
        }
    }
}

// Fetch designations
$sql_designations = "SELECT designation_id, designation_name FROM Designation WHERE designation_status = 1";
$result_designations = $conn->query($sql_designations);

// Fetch states
$sql_states = "SELECT state_id, state_name FROM state WHERE state_status=1";
$result_states = $conn->query($sql_states);

// Fetch districts based on selected state
$state = $_POST['state'] ?? $state;
$state_name = '';
if (!empty($state)) {
    $sql_state_name = "SELECT state_name FROM state WHERE state_id = ?";
    $stmt = $conn->prepare($sql_state_name);
    $stmt->bind_param("i", $state);
    $stmt->execute();
    $stmt->bind_result($state_name);
    $stmt->fetch();
    $stmt->close();
}

// Fetch districts
$sql_districts = "SELECT district_id, district_name FROM district WHERE state_id = ?";
$stmt = $conn->prepare($sql_districts);
$stmt->bind_param("i", $state);
$stmt->execute();
$result_districts = $stmt->get_result();
$districts = [];
while ($row = $result_districts->fetch_assoc()) {
    $districts[] = $row;
}

// Fetch district name if district ID is provided
$district = $_POST['district'] ?? $district;
$district_name = '';
if (!empty($district)) {
    $sql_district_name = "SELECT district_name FROM district WHERE district_id = ?";
    $stmt = $conn->prepare($sql_district_name);
    $stmt->bind_param("i", $district);
    $stmt->execute();
    $stmt->bind_result($district_name);
    $stmt->fetch();
    $stmt->close();
}
?>

<?php include "./includes/header.php" ?>
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="./css/managestyles.css">
<link rel="stylesheet" href="./css/add-emp-styles.css">

<main>
    <link rel="stylesheet" href="./css/managestyles.css">
    <div class="container-fluid px-4">
        <h4 class="mt-4">Edit employee</h4>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="./manage-employe.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Edit employee</li>
        </ol>
        <div class="card mb-4" style="margin-top: 20px;">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Edit employee
            </div>
            <div class="card-body">
                <div id="addemp-div">
                    <div class="col-xl-6">
                        <form class="bodycard" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post" onsubmit="return validateForm()" enctype="multipart/form-data">
                            <div class="mb-3 form-group">
                                <label for="name" class="form-label">Name:</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" oninput="validateName('name', 'nameErr')">
                                <span class="text-danger" id="nameErr"><?php echo $nameErr; ?></span>
                            </div>
                            <div class="mb-3 form-group">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" oninput="validateEmail('email', 'emailErr')">
                                <span style="color: red;" id="emailErr"><?php echo $emailErr; ?></span>
                            </div>
                            <div class="mb-3 form-group">
                                <label for="phone_number" class="form-label">Phone Number:</label>
                                <input type="number" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" oninput="validatePhoneNumber('phone_number', 'phoneNumberErr')" />
                                <span style="color: red;" id="phoneNumberErr"><?php echo $phoneErr; ?></span>
                            </div>

                            <div class="mb-3 form-group">
                                <label for="designation" class="form-label">Designation:</label>
                                <div style="position: relative; display: inline-block; width: 100%;">
                                    <select class="form-control" id="designation" name="designation" oninput="validateDesignation('designation','designationErr')" style="width: 100%; -webkit-appearance: none; -moz-appearance: none; appearance: none; padding-right: 30px;">
                                        <option value="" disabled selected>Select Designation</option>
                                        <?php while ($row = $result_designations->fetch_assoc()) { ?>
                                            <option value="<?php echo $row['designation_id']; ?>" <?php echo ($row['designation_id'] == $designation) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($row['designation_name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: black; font-size: 0.75em;">&#9660;</span>
                                </div>
                                <span id="designationErr" style="color: red;"><?php echo $designationErr; ?></span>
                            </div>
                            
                            <div class="mb-3 form-group" style="margin-top: 6px;">
                                <label for="state" class="form-label">State:</label>
                                <div style="position: relative; display: inline-block; width: 100%;">
                                    <div class="custom-dropdown">
                                        <div class="selected-state" id="selectedState"><?php echo ($state) ? htmlspecialchars($state_name) : 'Select State'; ?></div>
                                        <div class="dropdown-content" id="stateDropdownContent">
                                            <input type="text" id="stateSearch" placeholder="Search for states..." onkeyup="filterStates()">
                                            <ul class="dropdown-list" id="stateList">
                                                <li data-value="" class="disabled">Select State</li>
                                                <?php while ($row = $result_states->fetch_assoc()) { ?>
                                                    <li data-value="<?php echo $row['state_id']; ?>" <?php echo ($row['state_id'] == $state) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($row['state_name']); ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: black; font-size: 0.75em;">&#9660;</span>
                                    </div>
                                    <input type="hidden" name="state" id="state" value="<?php echo $state; ?>">
                                    <span id="stateErr" style="color: red;"><?php echo $stateErr; ?></span>
                                </div>
                            </div>

                            <div class="mb-3 form-group" style="margin-top: 6px;">
                                <label for="district" class="form-label">District:</label>
                                <div style="position: relative; display: inline-block; width: 100%;">
                                    <div class="custom-dropdown">
                                        <div class="selected-district" id="selectedDistrict"><?php echo ($district) ? htmlspecialchars($district_name) : 'Select District'; ?></div>
                                        <div class="dropdown-content" id="districtDropdownContent">
                                            <input type="text" id="districtSearch" placeholder="Search for districts..." onkeyup="filterDistricts()">
                                            <ul class="dropdown-list" id="districtList">
                                                <li data-value="" class="disabled">Select District</li>
                                                <?php foreach ($districts as $districtRow) { ?>
                                                    <li data-value="<?php echo $districtRow['district_id']; ?>" <?php echo ($districtRow['district_id'] == $district) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($districtRow['district_name']); ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: black; font-size: 0.75em;">&#9660;</span>
                                    </div>
                                    <input type="hidden" name="district" id="district" value="<?php echo $district; ?>">
                                    <span class="text-danger"><?php echo $districtErr; ?></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Profile Picture:</label>
                                <input type="file" id="profile_picture" class="form-control" name="profile_picture" onchange="previewImage(event)">
                                <?php if ($profile_picture) { ?>
                                    <img src="emp_profile_upload/<?php echo $profile_picture; ?>" class="profile-img" id="profile_preview" alt="Profile Picture">
                                <?php } else { ?>
                                    <img src="" class="profile-img" id="profile_preview" alt="Profile Picture" style="display: none;">
                                <?php } ?>
                                <span style="color: red;"><?php echo $imageErr; ?></span>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" style="margin-top: 44px;" class="form-check-input" id="employee_status" name="employee_status" <?php echo ($emp_status == 1) ? 'checked' : ''; ?> />
                                <label style="margin-top: 40px;" class="form-check-label" for="employee_status">Status</label>
                                <span class="text-danger"><?php echo $emp_statusErr; ?></span><br>
                            </div>
                            <button style="width: 140px; height:40px; float:right; margin-left:196px; margin-top: 100px;" type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="./js/common.js"></script>
<script src="./js/state-desyricts-filters.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    function updateDistricts(stateId) {
        $.ajax({
            url: 'fetch-districts.php',
            type: 'POST',
            data: {
                state_id: stateId
            },
            success: function(data) {
                $('#district').html(data);
            }
        });
    }

    $(document).ready(function() {
        $('#state').change(function() {
            updateDistricts($(this).val());
        });
        // Preselect district based on current value
        $('#state').trigger('change');
    });
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('profile_preview');
            output.src = reader.result;
            output.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
<?php include "./includes/footer.php"; ?>
