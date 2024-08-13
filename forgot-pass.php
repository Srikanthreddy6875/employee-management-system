<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Forgot Password - Task Reminder</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
</head>
<body class="bg-primary">
    <?php
    session_start();
    require_once "./connection.php";

    $emailErr = $current_passErr = $new_passErr = $confirm_passErr = "";
    $email = $current_pass = $new_pass = $confirm_pass = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["email"])) {
            $emailErr = "* Email is required";
        } else {
            $email = trim($_POST["email"]);
        }

        if (empty($_POST["current_pass"])) {
            $current_passErr = "* Current Password is required";
        } else {
            $current_pass = trim($_POST["current_pass"]);
        }

        if (empty($_POST["new_pass"])) {
            $new_passErr = "* New Password is required";
        } else {
            $new_pass = trim($_POST["new_pass"]);
        }

        if (empty($_POST["confirm_pass"])) {
            $confirm_passErr = "* Please confirm new password";
        } else {
            $confirm_pass = trim($_POST["confirm_pass"]);
        }

        if (!empty($email) && !empty($current_pass) && !empty($new_pass) && !empty($confirm_pass)) {
            // Check if email exists
            $stmt = $conn->prepare("SELECT password FROM admin WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                // Debugging: Check the hashed password from database
                // echo "<pre>"; print_r($row); echo "</pre>"; // Uncomment for debugging
                
                // Direct password comparison (without hashing)
                if ($current_pass === $row['password']) {
                    if ($new_pass === $confirm_pass) {
                       
                        $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE email = ?");
                        $stmt->bind_param("ss", $new_pass, $email);
                        if ($stmt->execute()) {

                            echo "<div class='alert alert-success'>Password updated successfully!</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Error updating password.</div>";
                        }
                        $stmt->close();
                    } else {
                        $confirm_passErr = "* New passwords do not match";
                    }
                } else {
                    $current_passErr = "* Current password is incorrect";
                }
            } else {
                $emailErr = "* Email not found";
            }

            $conn->close();
        }
    }
    ?>

    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Forgot Password</h3></div>
                                <div class="card-body">
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm();">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="email" type="email" placeholder="Email Address" name="email" value="<?php echo htmlspecialchars($email); ?>" />
                                            <label for="email">Email Address</label>
                                            <span id="emailErr" class="error-message" style="color: red;"><?php echo $emailErr; ?></span>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="current_pass" type="password" placeholder="Current Password" name="current_pass" />
                                            <label for="current_pass">Current Password</label>
                                            <span id="current_passErr" class="error-message" style="color: red;"><?php echo $current_passErr; ?></span>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="new_pass" type="password" placeholder="New Password" name="new_pass" />
                                            <label for="new_pass">New Password</label>
                                            <span id="new_passErr" class="error-message" style="color: red;"><?php echo $new_passErr; ?></span>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="confirm_pass" type="password" placeholder="Confirm New Password" name="confirm_pass" />
                                            <label for="confirm_pass">Confirm New Password</label>
                                            <span id="confirm_passErr" class="error-message" style="color: red;"><?php echo $confirm_passErr; ?></span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button type="submit" class="btn btn-primary">Reset Password</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="index.php">Back to login</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
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
    <script>
    function validateForm() {
        var email = document.getElementById("email").value;
        var current_pass = document.getElementById("current_pass").value;
        var new_pass = document.getElementById("new_pass").value;
        var confirm_pass = document.getElementById("confirm_pass").value;
        var valid = true;

        if (email === "") {
            document.getElementById("emailErr").innerHTML = "* Email is required";
            valid = false;
        } else {
            document.getElementById("emailErr").innerHTML = "";
        }

        if (current_pass === "") {
            document.getElementById("current_passErr").innerHTML = "* Current Password is required";
            valid = false;
        } else {
            document.getElementById("current_passErr").innerHTML = "";
        }

        if (new_pass === "") {
            document.getElementById("new_passErr").innerHTML = "* New Password is required";
            valid = false;
        } else {
            document.getElementById("new_passErr").innerHTML = "";
        }

        if (confirm_pass === "") {
            document.getElementById("confirm_passErr").innerHTML = "* Please confirm new password";
            valid = false;
        } else if (new_pass !== confirm_pass) {
            document.getElementById("confirm_passErr").innerHTML = "* New passwords do not match";
            valid = false;
        } else {
            document.getElementById("confirm_passErr").innerHTML = "";
        }

        return valid;
    }
    </script>
</body>
</html>
