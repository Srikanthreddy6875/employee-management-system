<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Login - Dashboard</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<?php
$email_err = $pass_err = $login_err = "";
$email = $pass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["email"])) {
        $email_err = "* Email cannot be empty";
    } else {
        $email = htmlspecialchars($_POST["email"]);
    }

    if (empty($_POST["password"])) {
        $pass_err = "* Password cannot be empty";
    } else {
        $pass = htmlspecialchars($_POST["password"]);
    }

    if (!empty($email) && !empty($pass)) {
        require_once "./connection.php";

        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $rows = $result->fetch_assoc();

            if ($rows["password"] === $pass) {
                session_start();
                $_SESSION["email"] = $rows["email"];
                $_SESSION["user_type"] = $rows["user_type"];
                header("Location: login-check.php"); // Redirect to login_check.php
                exit();
            } else {
                $login_err = "<div class='alert alert-warning alert-dismissible fade show'>
                    <strong>Invalid Password</strong>
                    <button type='button' class='close' data-dismiss='alert'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>";
            }
        } else {
            $login_err = "<div class='alert alert-warning alert-dismissible fade show'>
                <strong>Email not found</strong>
                <button type='button' class='close' data-dismiss='alert'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main id="main-container">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                                <div class="card-body">
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="return validateForm();">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="email" type="email" placeholder="name@example.com" name="email" value="<?php echo $email; ?>" />
                                            <label for="email">Email address</label>
                                            <span id="emailErr" class="error-message" style="color: red;"><?php echo $email_err; ?></span>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="password" type="password" placeholder="Password" name="password" value="<?php echo $pass; ?>" />
                                            <label for="password">Password</label>
                                            <span id="passErr" class="error-message" style="color: red;"><?php echo $pass_err; ?></span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="./forgot-pass.php">Forgot Password?</a>
                                            <button type="submit" class="btn btn-primary">Login</button>
                                        </div>
                                        <?php echo $login_err; ?>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="register.php">Need an account? Sign up!</a></div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
    function validateForm() {
        var email = document.getElementById("email").value;
        var pass = document.getElementById("password").value;
        var valid = true;

        if (email === "") {
            document.getElementById("emailErr").innerHTML = "* Email cannot be empty";
            valid = false;
        } else {
            document.getElementById("emailErr").innerHTML = "";
        }

        if (pass === "") {
            document.getElementById("passErr").innerHTML = "* Password cannot be empty";
            valid = false;
        } else {
            document.getElementById("passErr").innerHTML = "";
        }

        return valid;
    }
    </script>
</body>
</html>
