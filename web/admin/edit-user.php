<?php 
include "../includes/header.php";

if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];
    
    // Fetch user details
    $sql = "SELECT * FROM users WHERE userId = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "user not found.";
        exit;
    }
} else {
    echo "No user ID provided.";
    exit;
}

// Update user details
if (isset($_POST['update'])) {
    $userName = $_POST['username'];
    $userRole = $_POST['role'];
    $userEmail = $_POST['email'];

    $sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE userId = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userName, $userEmail, $userRole, $userId]);

    if ($stmt->rowCount() > 0) {
      echo "
      <script>
      window.location.replace('manage-users.php');
      </script>
      ";
      exit;
  }
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Edit user</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Edit user</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Edit user</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form method="POST">
              <div class="card-body">
                <div class="form-group">
                  <label for="userName">user Name</label>
                  <input type="text" name="username" class="form-control" id="userName" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                  <label for="userPhone">Role</label>
                  <input type="text" name="role" class="form-control" id="userRole" value="<?php echo htmlspecialchars($user['role']); ?>" required>
                </div>
                <div class="form-group">
                  <label for="userEmail">Email</label>
                  <input type="email" name="email" class="form-control" id="userEmail" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <button type="submit" class="btn btn-primary" name="update">Update</button>
                <a href="manage-users.php" class="btn btn-danger">Cancel</a>
              </div>
            </form>
          </div>
          <!-- /.card -->
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
