<?php 
include "../includes/header.php";
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$admin]);
$adminData = $stmt->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['update'])) {
    $newUsername = $_POST['username'];
    $newEmail = $_POST['email'];
    $sql = "UPDATE users SET username = ?, email = ? WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$newUsername, $newEmail, $_SESSION['username']]);
    if($stmt->rowCount() > 0) {
        $_SESSION['username'] = $newUsername;
        echo "<script>
        alert('Profile updated successfully.');
        window.location.replace('profile.php');
        </script>";
        
    } else {
        echo "Failed to update user profile.";
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
            <h1 class="m-0 text-dark ml-4">Admin profile</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php">Home</a></li>
              <li class="breadcrumb-item active">Account settings</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row px-4">
            <!-- left column -->
            <div class="col-md-12">
              <!-- general form elements -->
              <div class="card card-primary">

                <!-- /.card-header -->
                <!-- form start -->
                <form role="form" method = "POST">
                  <div class="card-body">
                    <div class="form-group">
                        <label>Admin name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($adminData['username']); ?>" required name = "username">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($adminData['email']); ?>" required name = "email">
                    </div>
                    <div class="form-group">
                        <label>Admin Registration Date</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($adminData['createdAt']); ?>" required name = "createdAt" disabled>
                    </div>                                        
                  </div>
                  <!-- /.card-body -->
  
                  <div class="card-footer pb-4">
                    <button type="submit" class="btn btn-primary" name ="update">
                        UPDATE
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
 <?php  
 include "../includes/footer.php";

 ?>