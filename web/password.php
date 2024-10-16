<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(isset($_POST['recover-submit']))
{
    require("includes/conn.php");
   
    $emailId = $_POST['email'];

    // Prepare statement
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email= :email");
    $stmt->bindParam(':email', $emailId, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row)
    {
        // Generate token
        $token = md5($emailId) . rand(10, 9999);

        // Calculate expiration date (5 minutes from now)
        $expFormat = mktime(date("H"), date("i") + 5, date("s"), date("m"), date("d"), date("Y"));
        $expDate = date("Y-m-d H:i:s", $expFormat);

        // Update user record with token and expiration date
        $stmt = $pdo->prepare("UPDATE users SET reset_link_token = :token, exp_date = :expDate WHERE email = :email");
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':expDate', $expDate, PDO::PARAM_STR);
        $stmt->bindParam(':email', $emailId, PDO::PARAM_STR);
        $stmt->execute();

        // Construct reset password link
        $link = "<a href='http://localhost/citizen/reset-password.php?key=".$emailId."&token=".$token."'>Click To Reset password</a>";

        // Send email using PHPMailer
        require 'vendor/autoload.php';

        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Username = "paccyhabi@gmail.com"; // Replace with your Gmail address
        $mail->Password = "xzec mayt wrwu ewch"; // Replace with your Gmail password
        $mail->SMTPSecure = "ssl";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->From = "paccyhabi@gmail.com";
        $mail->FromName = 'Project verification management system';
        $mail->SMTPDebug = 0;
        $mail->addAddress($emailId, $row['username']);
        $mail->Subject = 'Reset Password';
        $mail->isHTML(true);
        $mail->Body = '
    <p>Hello ' . $row['username'] . ',</p>
    <p>You requested to reset your password for your account.</p>
    <p>To reset your password, please click the link below:</p>
    <p><a href="http://localhost/citizen/reset-password.php?key=' . $emailId . '&token=' . $token . '">Click To Reset Password</a></p>
    <p>This link will expire in 5 minutes for your security. If you did not request a password reset, please ignore this email.</p>
    <p>Thank you!</p>
    <p>Best regards,<br>Your Project Verification Management System</p>
';


        if($mail->Send())
        {
            $message = "Check Your Email and Click on the link sent to your email";
        }
        else
        {
            echo "Mail Error - >".$mail->ErrorInfo;
        }
    }
    else
    {
        echo "<p style='color:red;'>Invalid Email Address</p>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <style>
        .form-gap {
            padding-top: 70px;
        }
    </style>
</head>
<body>
<div class="form-gap"></div>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="text-center">
                        <h3><i class="fa fa-lock fa-4x"></i></h3>
                        <h2 class="text-center">Forgot Password?</h2>
                        <p>You can reset your password here.</p>
                        <div class="panel-body">
                            <form id="register-form" role="form" autocomplete="off" class="form" method="post">
                                <?php if (isset($message)): ?>
                                    <div class="alert alert-success"><strong><?= $message ?></strong></div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-envelope color-blue"></i></span>
                                        <input id="email" name="email" placeholder="email address" class="form-control" type="email" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input name="recover-submit" class="btn btn-lg btn-primary btn-block" value="Reset Password" type="submit">
                                </div>
                                <input type="hidden" class="hide" name="token" id="token" value="">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>