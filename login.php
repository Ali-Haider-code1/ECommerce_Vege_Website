<?php
session_start();
require_once("db.php");
include_once("navbar.php");


$errors = array();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    if (empty($email)) {
        $errors[] = "Email address is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }

    // Validate Password
    $password = trim($_POST["password"]);
    if (empty($password)) {
        $errors[] = "Password is required";
    }

    // If there are no validation errors, proceed with database check
    if (empty($errors)) {
        // Query to check if the user with the provided email and password exists
        $sql = "SELECT * FROM user WHERE email = '$email'";
        $result = mysqli_query($con, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row && password_verify($password, $row['password'])) {
                $_SESSION['email'] = $row['email'];

                echo '<script>alert("Login successful!");</script>';
                echo '<script>window.location.href = "index.php";</script>';
            } else {
                $errors[] = "Invalid email or password";
            }
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    }
    if (!empty($errors)) {
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

        .form-label {
            position: relative;
            left: -75px;
        }

        .email {
            position: relative;
            left: -195px;
        }

        .pass {

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
                        <h2 class="fw-bold mb-5">Login to your account</h2>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <!-- 2 column grid layout with text inputs for the first and last names -->
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
                                Login
                            </button>
                            <a href="signup.php"><span class="text-center fw-bold">Dont't have an account? Creat One</span></a>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-5 mb-lg-0">
                <img src="./images/bg_2.jpg" class="w-100 rounded-4 shadow-4" alt="" />
            </div>
        </div>
    </div>
    <!-- Jumbotron -->
</section>
<!-- Section: Design Block -->

</body>

</html>