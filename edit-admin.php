<?php
session_start();
require_once "./connection.php";

$user_type = isset($_SESSION["user_type"]) ? htmlspecialchars($_SESSION["user_type"]) : 'Unknown';

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Add New Employee - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="./css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .bodycard {
            width: 76vw;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .form-group label {
            display: block;
        }

        .form-group input,
        .form-group select {
            padding: 8px;
            margin-top: 5px;
        }

        .form-group input[type=radio] {
            width: auto;
        }

        .form-group span {
            color: red;
            font-size: 12px;
        }

        #addem_btn {
            width: 140px;
            /* height: 40px; */
            margin: 17px 0px 0px 50px;
            margin-left: 390px;

        }
    </style>

</head>
<?php
ob_start();
require_once "./connection.php";

$id = $_GET["id"];

$sql = "SELECT * FROM admin WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Admin not found.";
    exit;
}

$row = $result->fetch_assoc();
$name = $row["name"];
$email = $row["email"];
$dob = $row["dob"];
$user_type = $row["user_type"];
$admin_status = $row["admin_status"];

$nameErr = $emailErr = $dobErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newName = trim($_POST["name"]);
    $newEmail = trim($_POST["email"]);
    $newDob = $_POST["dob"];
    $newUserType = trim($_POST["user_type"]);
    $newAdminStatus = isset($_POST["admin_status"]) ? 1 : 0;

    // Validate name
    if (empty($newName)) {
        $nameErr = "<p class='error'>* Name is required</p>";
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $newName)) {
        $nameErr = "<p class='error'>* Only letters and white space allowed</p>";
    }

    // Validate email
    if (empty($newEmail)) {
        $emailErr = "<p class='error'>* Email is required</p>";
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "<p class='error'>* Invalid email format</p>";
    } else {
        // Check if the new email is already registered and it's not the current admin's email
        $sql_select_query = "SELECT email FROM admin WHERE email = ? AND id != ?";
        $stmt = $conn->prepare($sql_select_query);
        $stmt->bind_param("si", $newEmail, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $emailErr = "<p class='error'>* Email already registered</p>";
        }
    }

    // Validate date of birth
    if (empty($newDob)) {
        $dobErr = "<p class='error'>* Date of Birth is required</p>";
    }

    if (empty($nameErr) && empty($emailErr) && empty($dobErr)) {
        $sql_update_query = "UPDATE admin SET name = ?, email = ?, dob = ?, user_type = ?, admin_status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update_query);
        $stmt->bind_param("ssssii", $newName, $newEmail, $newDob, $newUserType, $newAdminStatus, $id);

        if ($stmt->execute()) {
            header("Location: manage-admin.php");
        } else {
            echo "<span class='error'>Error: " . mysqli_error($conn) . "</span>";
        }
    }
}
ob_end_flush();
?>
<?php include "./includes/header.php"  ?>
            <main>
                <div class="container-fluid px-4">
                    <h4 class="mt-4">Edit User</h4>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="./manage-admin.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit User</li>
                    </ol>
                    <div class="card mb-4" style="margin-top: 20px;">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Edit user Details
                        </div>
                        <div class="card-body">
                            <div id="addemp-div">
                                <div class="col-xl-6">
                                    <form class="bodycard" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?id=" . $id; ?>" onsubmit="return validateForm()">
                                        <div class="form-group">
                                            <label>Full Name :</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($name); ?>" name="name" id="name" oninput="validateName('name', 'nameErr')">
                                            <span id="nameErr" class="error"><?php echo $nameErr; ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label>Email :</label>
                                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" name="email" id="email" oninput=" validateEmail('email', 'emailErr')">
                                            <span id="emailErr" class="error"><?php echo $emailErr; ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label>Date-of-Birth :</label>
                                            <input type="date" class="form-control" value="<?php echo htmlspecialchars($dob); ?>" name="dob" id="dob" oninput=" validateDOB('dob', 'dobErr')">
                                            <span id="dobErr" style="color: red;"><?php echo $dobErr; ?></span>
                                        </div>

                                        <div class="form-group">
                                            <label>User Type :</label>
                                            <div style="position: relative; display: inline-block; width: 100%;">
                                                <select class="form-control" name="user_type" id="user_type" oninput="validateUserType('user_type','userTypeErr')" style="width: 100%; -webkit-appearance: none; -moz-appearance: none; appearance: none; padding-right: 30px;">
                                                    <option value="Super Admin" <?php if ($user_type == "Super Admin") echo "selected"; ?>>Super Admin</option>
                                                    <option value="Admin" <?php if ($user_type == "Admin") echo "selected"; ?>>Admin</option>
                                                    <option value="Sub Admin" <?php if ($user_type == "Sub Admin") echo "selected"; ?>>Sub Admin</option>
                                                </select>
                                                <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: black;">&#9660;</span>
                                            </div>
                                            <span id="userTypeErr" style="color: red;"></span>
                                        </div>

                                        <div class="form-group">
                                            Status:<input type="checkbox" name="admin_status" id="admin_status" value="1" <?php echo ($admin_status == 1) ? 'checked' : ''; ?>><br>
                                        </div>

                                        <!-- <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="Toolbar with button groups"> -->
                                        <!-- <div class="btn-group"> -->
                                        <!-- </div> -->
                                        <!-- <div class="input-group">
                                             </div> -->
                                        <!-- <input style="float: right; width:140px" type="submit" value="Save Changes" class="btn btn-primary w-20" name="save_changes"> -->
                                        <button style="float: right;" type="submit" name="save_changes" id="addem_btn" class="btn btn-primary">Save </button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
            </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="./js/common.js"></script>
    <?php include "./includes/header.php"  ?>