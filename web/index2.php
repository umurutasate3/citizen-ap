<?php
session_start();
include_once "includes/conn.php";
$message = '';
$login_failed = false;

if (isset($_POST['login'])) {
    $username = $_POST["username"];
    $password = sha1($_POST["password"]);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ? AND isActive = 1");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
            header("location:./admin/");
        exit;
    } else {
        $message = 'Invalid username or password';
        $login_failed = true;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Add Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom CSS -->
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        header {
            background-image: url("./dist/img/8.jpg");
            height: 100vh;
            background-size: cover;
            background-position: center;
        }
        .navbar-brand img {
            width: 90px;
            height: auto;
        }
        .footer {
            background-color: #333;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            margin-top: 13rem;
        }
        img {
            border-radius: 5px;
            box-shadow: #333;
        }
        span {
            color: red;
        }
        h1 {
            color: white;
            text-transform: uppercase;
            font-size: 40px;
            text-align: center;
        }
        .nav-item a {
            color: white;
            text-decoration: none;
            padding: 5px 20px;
            font-family: Roboto;
            font-size: 20px;
        }
        .nav-item a:hover {
            color: darkorange;
            text-decoration: none;
            padding: 5px 20px;
            font-family: Roboto;
            font-size: 20px;
        }        
        .btn-color {
            background-color: darkorange;
        }
        .btn-outline-warning:hover {
            background-color: darkorange;
        }
        .contact-form {
            background: #fff;
            margin-top: 5%;
            margin-bottom: 5%;
            width: 70%;
            padding: 3%;
            border-radius: 5px;
            box-shadow: 0px 0 10px rgba(0, 0, 0, 0.1);
        }
        .contact-form .form-control {
            border-radius: 1rem;
        }
        .contact-form h3 {
            margin-bottom: 10%;
            text-align: center;
            color: #333;
        }
        .contact-form .btnContact {
            width: 50%;
            border: none;
            border-radius: 1rem;
            padding: 1.5%;
            background: darkorange;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
        }
        .contact-form .btnContactSubmit {
            width: 50%;
            border: none;
            border-radius: 1rem;
            padding: 1.5%;
            background-color: darkorange;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
        }
        .help-section {
            padding: 5%;
            background: #fff;
            margin: 5% auto;
            width: 70%;
            border-radius: 5px;
            box-shadow: 0px 0 10px rgba(0, 0, 0, 0.1);
        }
        .help-section h3 {
            text-align: center;
            color: #333;
        }
        .help-section p {
            font-size: 1.1rem;
            margin-top: 1.5rem;
        }
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
            background-color: darkorange;
            color: white;
            width: 40px;
            height: 40px;
            text-align: center;
            line-height: 40px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 1000;
        }
        
    .modal-content {
        border-radius: 10px;
    }
    .modal-header {
        border-bottom: none;
    }
    .modal-body {
        padding: 2rem;
    }
    .form-control {
        border-radius: 5px;
    }
    .btn-primary {
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        transition: background-color 0.3s;
    }
    .btn-primary:hover {
        background-color: #0056b3;
    }

        .alert {
            margin-bottom: 15px;
        }

    </style>
</head>
<body>
    <!-- Jumbotron Header -->
    <header>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
            <a class="navbar-brand" href="#"><img src="./dist/img/2.png" alt="citizen appointment system Logo"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item mr-2">
                        <a href="#homeSection" class="home.php">Home</a>
                    </li>
                    <li class="nav-item mr-2">
                        <a href="#contactSection" class="">Contact</a>
                    </li>
                    <li class="nav-item mr-2">
                        <a href="#helpSection" class="">Help</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="container-fluid" id="homeSection">
            <div class="row mt-5">
                <div class="col text-center mt-5">
                <h1 class="mt-5 mb-5">Schedule Your Appointment Today!</h1>
                    <p class="lead">Easily book your appointment with your cell officer and manage your time efficiently.</p>
            
                    <div class="button">
                        <a href="https://youtu.be/qvZ-Og3sTGg" class="btn btn-color text-white mr-1">Watch Video</a>
                        <a href="#" class="btn btn-outline-primary ml-1 text-white" data-toggle="modal" data-target="#loginModal">Discover...</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize message variable
$message = '';

if (isset($_POST['btnSubmit'])) {
    require 'vendor/autoload.php'; // Include PHPMailer autoload file

    // Collect form data
    $name = htmlspecialchars(trim($_POST['txtName']));
    $email = htmlspecialchars(trim($_POST['txtEmail']));
    $phone = htmlspecialchars(trim($_POST['txtPhone']));
    $messageContent = htmlspecialchars(trim($_POST['txtMsg']));

    // Create a new PHPMailer instance
    $mail = new PHPMailer();
    $mail->CharSet = "utf-8";
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->Username = "claudeumurutasate4@gmail.com";
    $mail->Password = "uxhb qwgi pfes lpny"; 
    $mail->SMTPSecure = "ssl";
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465;
    $mail->From = "claudeumurutasate4@gmail.com"; 
    $mail->FromName = 'Citizen appointment System || Contact Form';
    $mail->addAddress("claudeumurutasate4@gmail.com"); 
    $mail->Subject = 'New Contact Message from ' . $name;
    $mail->isHTML(true);
    $mail->Body = "
        <p>You have received a new message from your contact form:</p>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Message:</strong><br>$messageContent</p>
    ";

    // Send the email
    if ($mail->Send()) {
        $message = '<div class="alert alert-success">Your message has been sent successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Mail Error: ' . $mail->ErrorInfo . '</div>';
    }
}
?>

<!-- Contact Section -->
<section id="contactSection">
    <div class="container contact-form">
        <form method="post">
            <h3>Drop Us a Message</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" name="txtName" class="form-control" placeholder="Your Name *" required />
                    </div>
                    <div class="form-group">
                        <input type="email" name="txtEmail" class="form-control" placeholder="Your Email *" required />
                    </div>
                    <div class="form-group">
                        <input type="text" name="txtPhone" class="form-control" placeholder="Your Phone Number *" required />
                    </div>
                    <div class="form-group">
                        <input type="submit" name="btnSubmit" class="btnContact" value="Send Message" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <textarea name="txtMsg" class="form-control" placeholder="Your Message *" style="width: 100%; height: 150px;" required></textarea>
                    </div>
                </div>
            </div>
            <!-- Display feedback message here -->
            <?php if (!empty($message)): ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $message; ?>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>
</section>


    <!-- Help Section -->
<section id="helpSection">
    <div class="container help-section">
        <h3>Help & FAQs</h3>
        <p><strong>Q1: How do I schedule an appointment?</strong></p>
        <p>A: To schedule an appointment, go to the "Book Appointment" section and follow the instructions provided.</p>
        <p><strong>Q2: How do I contact my cell officer?</strong></p>
        <p>A: You can contact your cell officer directly after scheduling an appointment or through the contact form on the Contact Us page.</p>
        <p><strong>Q3: Can I reschedule or cancel an appointment?</strong></p>
        <p>A: Yes, you can reschedule or cancel an appointment by logging into your account and visiting the "Manage Appointments" section.</p>
        <p>If you have any other questions, please feel free to <a href="#contactSection">contact us</a>.</p>
    </div>
</section>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTopBtn"><i class="fas fa-arrow-up"></i></a>
<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login to Your Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if ($login_failed): ?>
                    <div class="alert alert-danger">Invalid username or password</div>
                    <p><a href="./password" class="text-decoration-none" id="forgotPasswordLink">Forgot Password?</a></p>
                <?php endif; ?>
                
                <form method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        <small><input type="checkbox" onclick="Toggle()"> Show password</small>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary btn-block" name="login">Login</button>
                    </div>
                </form>
                <hr>
                <div class="text-center">
                    <p class="mb-0">Don't have an account? <a href="./create-account" class="text-decoration-none">Create Account</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function Toggle() {
        var passwordField = document.getElementById("password");
        if (passwordField.type === "password") {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }
</script>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Trigger the login modal to show if there is an error message
        $(document).ready(function() {
            <?php if ($login_failed): ?>
                $('#loginModal').modal('show');
            <?php endif; ?>

            // Toggle the icon from plus to minus when clicking
            $('.toggle-details').on('click', function() {
                $(this).toggleClass('fa-plus fa-minus');
            });
        });
        var desc = document.getElementById('desc');
        <?php if (!empty($projects)): ?>
                desc.style.display = 'none';
        <?php endif; ?>
    </script>
    <script>
  function Toggle() { 
    var temp = document.getElementById("password"); 
    if (temp.type === "password" ) { 
      temp.type = "text"; 
    } 
    else { 
      temp.type = "password"; 
    } 
      }
</script>
    <script>
        // Smooth scrolling functionality
        $(document).ready(function() {
            // Show or hide the sticky footer button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 200) {
                    $('.back-to-top').fadeIn(200);
                } else {
                    $('.back-to-top').fadeOut(200);
                }
            });
            
            // Animate the scroll to top
            $('.back-to-top').click(function(event) {
                event.preventDefault();
                $('html, body').animate({scrollTop: 0}, 300);
            })
        });
    </script>
</body>
</html>
