<?php
include "../includes/header.php";

if (isset($_POST['update'])) {
    $currentPassword = $_POST['password'];
    $newPassword = $_POST['password1'];
    $confirmPassword = $_POST['password2'];

    // Fetch the current hashed password from the database
    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
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

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark ml-4">Change password</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Account settings</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row px-4">
          <div class="col-md-12">
            <div class="card card-primary">
              <!-- /.card-header -->
              <!-- form start -->
              <form role="form" method="POST">
                <div class="card-body">
                  <?php if (isset($message)): ?>
                    <div class="alert alert-danger"><?= $message ?></div>
                  <?php endif; ?>
                  <?php if (isset($message1)): ?>
                    <div class="alert alert-success"><?= $message1 ?></div>
                  <?php endif; ?>                  
                  <div class="form-group">
                      <label>Current password</label>
                      <input type="password" class="form-control" placeholder="Enter your current password" required name="password">
                  </div>
                  <div class="form-group">
                      <label>New password</label>
                      <input type="password" class="form-control" placeholder="Enter new password" required name="password1" id="exampleInputPassword1">
                  </div>
                  <div class="form-group">
                      <label>Confirm password</label>
                      <input type="password" class="form-control" placeholder="Confirm password" required name="password2" id="exampleInputPassword2">
                  </div>
                  <div class="form-group mt-2">
                      <input type="checkbox" onclick="Toggle()"> 
                      <label for="">Show password</label>
                  </div>                                     
                <!-- /.card-body -->

                <div class="card-footer pb-4">
                  <button type="submit" class="btn btn-primary" name="update">
                      UPDATE
                  </button>
                  <a href="index.php" class="btn btn-danger">Cancel</a>
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<script>
  function Toggle() { 
    var temp = document.getElementById("exampleInputPassword1"); 
    var temp2 = document.getElementById("exampleInputPassword2"); 
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
<?php  
include "../includes/footer.php";
?>
