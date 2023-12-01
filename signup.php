<?php include_once("navbar.php");?>
<?php
require_once("db.php");

$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST["fname"]);
    if (empty($fname)) {
        $errors[] = "First name is required";
    }
    $lname = trim($_POST["lname"]);
    if (empty($lname)) {
        $errors[] = "Last name is required";
    }
    $email = trim($_POST["email"]);
    if (empty($email)) {
        $errors[] = "Email address is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }
    $password = trim($_POST["password"]);
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO user (fname, lname, email, password) VALUES ('$fname', '$lname', '$email', '$hashedPassword')";
        if (mysqli_query($con, $sql)) {
            echo '<script>alert("Signup successful!");
            window.location.href="login.php";
            </script>';
            
        } else {
            echo '<script>alert("Error: ' . $sql . '\n' . mysqli_error($con) . '");</script>';
        }
    } else {
        foreach ($errors as $error) {
            echo '<script>alert("' . $error . '");</script>';
        }
    }
}
?>
    <section class="text-center text-lg-start">
        <style>
            .cascading-right {
                margin-right: -50px;
            }

            @media (max-width: 991.98px) {
                .cascading-right {
                    margin-right: 0;
                }
            }
            .form-label{
                position: relative;
                left: -75px;
            }
            .email{
                position: relative;
                left: -195px;
            }
            .pass{
                
                left: -210px;
            }
        </style>

        <!-- Jumbotron -->
        <div class="container py-4">
            <div class="row g-0 align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="card cascading-right" style="
            background: hsla(0, 0%, 100%, 0.55);
            backdrop-filter: blur(30px);
            ">
                        <div class="card-body p-5 shadow-5 text-center">
                            <h2 class="fw-bold mb-5">Sign up now</h2>
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                <!-- 2 column grid layout with text inputs for the first and last names -->
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="form-outline">
                                            <label class="form-label" for="form3Example1">First name</label>
                                            <input type="text" id="form3Example1" class="form-control" name="fname"/>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="form-outline">
                                            <label class="form-label" for="form3Example2">Last name</label>
                                            <input type="text" id="form3Example2" class="form-control" name="lname" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Email input -->
                                <div class="form-outline mb-4">
                                    <label class="form-label email" for="form3Example3">Email address</label>
                                    <input type="email" id="form3Example3" class="form-control" name="email" />
                                </div>

                                <!-- Password input -->
                                <div class="form-outline mb-4">
                                    <label class="form-label pass" for="form3Example4">Password</label>
                                    <input type="password" id="form3Example4" class="form-control" name="password" />
                                </div>

                                <!-- Submit button -->
                                <button type="submit" class="btn btn-primary btn-block mb-4">
                                    Sign up
                                </button>
                                <a href="login.php"><span class="text-center fw-bold">Already have an account? Login</span></a>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-5 mb-lg-0">
                    <img src="./images/category-1.jpg" class="w-100 rounded-4 shadow-4" alt="" />
                </div>
            </div>
        </div>
        <!-- Jumbotron -->
    </section>
    <!-- Section: Design Block -->

</body>

</html>