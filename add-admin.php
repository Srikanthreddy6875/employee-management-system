<?php
session_start();
require_once "./connection.php";

$user_type = isset($_SESSION["user_type"]) ? htmlspecialchars($_SESSION["user_type"]) : 'Unknown';

if (!isset($_SESSION["user_type"])) {
    header("Location: ./index.php");
    exit();
}
$user_type = htmlspecialchars($_SESSION["user_type"]);

$nameErr = $emailErr = $passErr = $confirmPassErr = $dobErr = $userTypeErr  = $agreeErr = "";
$name = $email = $dob = $pass = $confirmPass = $user_type = $agree  = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_changes'])) {

    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $pass = htmlspecialchars($_POST["pass"]);
    $confirmPass = htmlspecialchars($_POST["confirm_pass"]);
    $dob = htmlspecialchars($_POST["dob"]);
    $user_type = htmlspecialchars($_POST['user_type']);
    $agree = isset($_POST['agree']) ? 1 : 0;

    // Validate form inputs
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
        // Check for duplicate email
        $email_check_query = "SELECT * FROM admin WHERE email='$email'";
        $result_email_check = mysqli_query($conn, $email_check_query);
        if (mysqli_num_rows($result_email_check) > 0) {
            $emailErr = "* Email already exists";
        }
    }

    if (empty($pass)) {
        $passErr = "* Password is required";
    } elseif (!preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}/", $pass)) {
        $passErr = "* Password must be at least 8 characters";
    }

    if (empty($confirmPass)) {
        $confirmPassErr = "* Confirm Password is required";
    } elseif ($pass !== $confirmPass) {
        $confirmPassErr = "* Passwords do not match";
    }

    if (empty($user_type)) {
        $userTypeErr = "* User Type is required";
    }

    if (empty($dob)) {
        $dobErr = "* Date of Birth is required";
    } else {
        $today = new DateTime();
        $selectedDate = new DateTime($dob);
        if ($selectedDate >= $today) {
            $dobErr = "* Date of Birth cannot be in the future";
        }
    }

    // Insert into database if no errors
    if (empty($nameErr) && empty($emailErr) && empty($passErr) && empty($confirmPassErr) && empty($userTypeErr) && empty($dobErr)) {
        $sql_insert_query = "INSERT INTO admin (name, email, password, dob, user_type, admin_status) 
                             VALUES ('$name', '$email', '$pass', '$dob', '$user_type', '$agree')";
        $result_insert = mysqli_query($conn, $sql_insert_query);

        if ($result_insert) {
            header("Location: manage-admin.php");
            exit();
        } else {
            echo "<span style='color:red'>Error: " . mysqli_error($conn) . "</span>";
        }
    }
}
?>

<?php include "./includes/header.php" ?>
            <main>
                <link rel="stylesheet" href="./css/add-admin.css">
                <div class="container-fluid px-4">
                    <h4 class="mt-4">Add New User</h4>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="./manage-admin.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Add User</li>
                    </ol>

                    <div class="card mb-4" id="addemp-div">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Add New User
                        </div>
                        <div class="card-body">
                            <form action="" method="POST" onsubmit="return validateForm()" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                                <div class="bodycard">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" id="name" class="form-control" oninput="validateName('name', 'nameErr')" />
                                        <span id="nameErr"><?php echo $nameErr; ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" id="email" class="form-control" oninput="validateEmail('email', 'emailErr')" />
                                        <span id="emailErr"><?php echo $emailErr; ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="dob">DOB</label>
                                        <input type="date" name="dob" id="dob" class="form-control" oninput="validateDOB('dob','dobErr')" />
                                        <span id="dobErr"><?php echo $dobErr; ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="pass">Password</label>
                                        <input type="password" name="pass" id="pass" class="form-control" oninput="validatePassword('pass','passErr')" />
                                        <span id="passErr"><?php echo $passErr; ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_pass">Confirm Password</label>
                                        <input type="password" name="confirm_pass" id="confirm_pass" class="form-control" oninput="validateConfirmPassword('confirm_pass','confirmPassErr')" />
                                        <span id="confirmPassErr"><?php echo $confirmPassErr; ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="usertype">User Type</label>
                                        <div style="position: relative; display: inline-block; width: 100%;">
                                            <select name="user_type" class="form-control" id="usertype" oninput="validatestate('usertype', 'userTypeErr')" style="width: 100%; -webkit-appearance: none; -moz-appearance: none; appearance: none; padding-right: 30px;">
                                                <option value="">Select</option>
                                                <option value="Super Admin">Super Admin</option>
                                                <option value="Admin">Admin</option>
                                                <option value="Sub Admin">Sub Admin</option>
                                            </select>
                                            <span style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: black;">&#9660;</span>
                                        </div>
                                        <span id="userTypeErr"><?php echo $userTypeErr; ?></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="agree"></label>
                                        <input type="checkbox" name="agree" id="agree" value="1" checked /> Status
                                    </div>
                                </div>
                                <button style="float: right; width:140px;" type="submit" name="save_changes" id="addem_btn" class="btn btn-primary ">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="./js/scripts.js"></script>
    <script src="./js/common.js"></script>
    <?php include "./includes/header.php" ?>