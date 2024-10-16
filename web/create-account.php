<?php
session_start();
include_once "./includes/conn.php";

$message = '';
if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = sha1($_POST['password1']);
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Check if the username already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $message = 'Username already exists!';
    } else {
        // Insert new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (username,email,phoneNumber,password) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username,$email,$phone,$password])) {
            $_SESSION["username"] = $username;
            $_SESSION["role"] = "umuturage";
            header("Location: ./includes/files.php");
            exit;
        } else {
            $message = 'Error creating account. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f7f9fc;
            font-family: 'Arial', sans-serif;
        }
        .card {
            border-radius: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 1rem;
        }
        .footer-link {
            text-align: center;
            margin-top: 15px;
        }
        img {
            border-radius: 25px;
        }
    </style>
</head>
<body>

<div class="container">
    <section class="vh-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-lg-12 col-xl-11">
                <div class="card text-black">
                    <div class="card-body p-md-5">
                        <div class="row justify-content-center">
                            <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
                                <h1 class="text-center fw-bold mb-5">Sign Up</h1>
                                <?php if (!empty($message)): ?>
                                    <div class="alert alert-danger text-center"><?= $message ?></div>
                                <?php endif; ?>
                                <form class="mx-1 mx-md-4" method="post">
                                    <div class="form-group mb-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <input type="text" id="form3Example1c" class="form-control" placeholder="Your Name" required name="username" />
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            </div>
                                            <input type="email" id="form3Example3c" class="form-control" placeholder="Your Email" required name = "email" />
                                        </div>
                                    </div>
                                    <div class="form-group mb-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            </div>
                                            <input type="text" id="form3Example1c" class="form-control" placeholder="Your phone number" required name = "phone" />
                                        </div>
                                    </div>                                    

                                    <div class="form-group mb-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            </div>
                                            <input type="password" id="password" class="form-control" placeholder="Password" required  name="password1"/>
                                        </div>
                                    </div>

                                    <div class="form-group mb-4">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                            </div>
                                            <input type="password" id="confirm_password" class="form-control" placeholder="Repeat your password" required  name="password2"/>
                                        </div>
                                    </div>

                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" value="" id="form2Example3c" required />
                                        <label class="form-check-label" for="form2Example3">
                                            I agree to the <a href="#!" class="text-primary">Terms of Service</a>
                                        </label>
                                    </div>

                                    <div class="d-flex justify-content-center mb-4">
                                        <button type="submit" class="btn btn-primary btn-lg" name="register">Register</button>
                                    </div>

                                    <div class="footer-link">
                                        <p class="mb-0">Already have an account? <a href="./" class="text-decoration-none text-primary">Login here</a></p>
                                    </div>
                                </form>

                            </div>
                            <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">
                                <img src="./dist/img/1.jpg" class="img-fluid" alt="Sample image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Form validation for matching passwords
    $('form').on('submit', function(event) {
        var password = $('#password').val();
        var confirmPassword = $('#confirm_password').val();
        if (password !== confirmPassword) {
            event.preventDefault();
            alert("Passwords do not match.");
        }
    });
</script>

</body>
</html>
