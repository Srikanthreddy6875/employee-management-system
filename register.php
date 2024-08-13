<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Register - Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="./css/managestyles.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .card-body {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .bodycard {
            width: 600px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .text-danger {
            color: red;
        }
    </style>
</head>

<?php
session_start();
require_once "./connection.php";

$nameErr = $emailErr = $passErr = $confirmPassErr = $userTypeErr = $dobErr = $agreeErr = "";
$name = $email = $pass = $confirmPass = $user_type = $dob = "";
$admin_status = 1; // Default status is active

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_changes'])) {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $pass = htmlspecialchars($_POST["pass"]);
    $confirmPass = htmlspecialchars($_POST["confirm_pass"]);
    $user_type = $_POST['user_type'];
    $dob = htmlspecialchars($_POST["dob"]);
    $admin_status = isset($_POST['admin_status']) ? 1 : 0; // Admin status is set based on checkbox

    if (empty($name)) {
        $nameErr = "* Name is required";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $nameErr = "* Name should contain only letters";
    }

    if (empty($email)) {
        $emailErr = "* Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "* Invalid email format";
    }

    if (empty($pass)) {
        $passErr = "* Password is required";
    } elseif (!preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}/", $pass)) {
        $passErr = "* Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character";
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

    if (empty($emailErr)) {
        $sql_check_email = "SELECT * FROM admin WHERE email=?";
        $stmt = $conn->prepare($sql_check_email);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result_check_email = $stmt->get_result();
        if ($result_check_email->num_rows > 0) {
            $emailErr = "Email already exists. Please use a different email.";
        }
        $stmt->close();
    }

    if (empty($nameErr) && empty($emailErr) && empty($passErr) && empty($confirmPassErr) && empty($userTypeErr) && empty($dobErr) && empty($agreeErr)) {
        $sql_insert_query = "INSERT INTO admin (name, email, password, dob, user_type, admin_status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert_query);
        $stmt->bind_param("sssssi", $name, $email, $pass, $dob, $user_type, $admin_status);
        $result_insert = $stmt->execute();

        if ($result_insert) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<span style='color:red'>Error: " . $stmt->error . "</span>";
        }
        $stmt->close();
    }
}
?>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Create Account</h3>
                                </div>
                                <div class="card-body">
                                    <form method="POST" class="bodycard" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="return validateForm();">
                                        <div class="form-group">
                                            <label for="name">Name:</label>
                                            <input type="text" class="form-control" placeholder="Enter name" value="<?php echo htmlspecialchars($name); ?>" name="name" id="name" oninput="validateName('name', 'nameErr')" />
                                            <span id="nameErr" style="color: red;"><?php echo $nameErr; ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email:</label>
                                            <input type="email" class="form-control" placeholder="Enter email" value="<?php echo htmlspecialchars($email); ?>" name="email" id="email" oninput="validateEmail('email', 'emailErr')" />
                                            <span id="emailErr" style="color: red;"><?php echo $emailErr; ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="pass">Password:</label>
                                            <input type="password" class="form-control" placeholder="Enter password" name="pass" id="pass" oninput="validatePassword('pass', 'passErr')" />
                                            <span id="passErr" style="color: red;"><?php echo $passErr; ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm_pass">Confirm Password:</label>
                                            <input type="password" class="form-control" placeholder="Confirm password" name="confirm_pass" id="confirm_pass" oninput="validateConfirmPassword('confirm_pass', 'confirmPassErr')" />
                                            <span id="confirmPassErr" style="color: red;"><?php echo $confirmPassErr; ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="user_type">User Type:</label>
                                            <select name="user_type" class="form-control" id="user_type" oninput="validateUserType('user_type','userTypeErr')">
                                                <option value="">Select User Type</option>
                                                <option value="Admin" <?php echo ($user_type == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                                <option value="Super Admin" <?php echo ($user_type == 'Super Admin') ? 'selected' : ''; ?>>Super Admin</option>
                                                <option value="Sub Admin" <?php echo ($user_type == 'Sub Admin') ? 'selected' : ''; ?>>Sub Admin</option>
                                            </select>
                                            <span id="userTypeErr" style="color: red;"><?php echo $userTypeErr; ?></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="dob">Date of Birth:</label>
                                            <input type="date" id="dob" class="form-control" placeholder="Enter date of birth" value="<?php echo htmlspecialchars($dob); ?>" name="dob" max="<?php echo date('Y-m-d'); ?>" oninput="validateDOB('dob', 'dobErr')" />
                                            <span id="dobErr" style="color: red;"><?php echo $dobErr; ?></span>
                                        </div>
                                        <div class="form-group">
                                            <input type="checkbox" name="admin_status" id="admin_status" checked />
                                            <label for="admin_status">Admin Status (Active)</label>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary" value="Save Changes" name="save_changes">
                                            <p>If already registered <a href="./index.php">click here</a></p>
                                            <span id="agreeErr" style="color: red;"><?php echo $agreeErr; ?></span>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="./index.php">Have an account? Login!</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="js/common.js"></script>
</body>

</html>
