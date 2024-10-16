<?php
session_start();
include_once "./includes/conn.php";
if(!isset($_SESSION['username'])){
    header("location:./index2.php");
  }else{
    $admin = $_SESSION['username'];
  }
if (isset($_POST['update'])) {
    $currentPassword = $_POST['password'];
    $newPassword = $_POST['password1'];
    $confirmPassword = $_POST['password2'];
    // Fetch the current hashed password from the database
    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ? AND role = 'student'");
    $stmt->execute([$admin]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user['password'] !== SHA1($currentPassword)) {
        $message = 'Current password is incorrect';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'New password and confirm password do not match';
    } else {
        // Hash the new password
        $hashedPassword = SHA1($newPassword);
        $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $updateStmt->execute([$hashedPassword, $admin]);

        if ($updateStmt->rowCount() > 0) {
            $message1 = 'Password updated successfully';
        } else {
            $message = 'Failed to update password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                    <?php if (isset($message)): ?>
                        <div class="alert alert-danger"><?= $message ?></div>
                    <?php endif; ?>
                    <?php if (isset($message1)): ?>
                        <div class="alert alert-success"><?= $message1 ?></div>
                    <?php endif; ?> 
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="password" required />
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="password1" required />
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="password2" required>
                        </div>
                        <div class="form-group mt-2">
                      <input type="checkbox" onclick="Toggle()"> 
                      <label for="">Show password</label>
                  </div>    
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary" name="update">Change Password</button>
                        </div>
                    </form>
                    <div class="text-center">
                        <a href="/mkul/home" class="text-decoration-none">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
  function Toggle() { 
    var temp = document.getElementById("newPassword"); 
    var temp2 = document.getElementById("confirmPassword"); 
    if (temp.type === "password" || temp2.type === "password" ) { 
      temp.type = "text"; 
      temp2.type = "text"; 
    } 
    else { 
      temp.type = "password"; 
      temp2.type = "password"; 
    } 
      }
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
