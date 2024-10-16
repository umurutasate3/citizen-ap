<?php 
include "../includes/header.php";
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Add Slot</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Add Slot</li>
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
            <div class="card-header">
              <h3 class="card-title">Slot Details</h3>
            </div>
            <form role="form" method="POST">
              <div class="card-body">                   
                <div class="form-group">
                  <label>Start Time</label>
                  <input type="datetime-local" class="form-control" id="startTime" required name="startTime">
                </div>
                <div class="form-group">
                  <label>End Time</label>
                  <input type="datetime-local" class="form-control" id="endTime" required name="endTime">
                </div>                               
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <button type="submit" class="btn btn-primary" name="addSlot">ADD</button>
              </div>
            </form>

        <script>
          // Set minimum date and time to prevent past dates
          const startTimeInput = document.getElementById('startTime');
          const now = new Date().toISOString().slice(0, 16); // Get current date and time in the correct format
          startTimeInput.min = now;

          // Ensure end time is after the start time
          const endTimeInput = document.getElementById('endTime');
          startTimeInput.addEventListener('change', function() {
            endTimeInput.min = startTimeInput.value; // Ensure end time is at least the start time
          });
        </script>

          </div>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>

<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<?php 
include "../includes/footer.php";
?>

<?php
if(isset($_POST['addSlot'])){
  $startTime = $_POST['startTime'];
  $endTime = $_POST['endTime'];
  
  // Format the datetime as Y-m-d H:i:s before inserting into the database
  $formattedStartTime = date('Y-m-d H:i:s', strtotime($startTime));
  $formattedEndTime = date('Y-m-d H:i:s', strtotime($endTime));

  $sql = "INSERT INTO slots (startTime, endTime, officerId) VALUES (:startTime, :endTime, :createdBy)";
  $stmt = $pdo->prepare($sql);
  if($stmt->execute(['startTime' => $formattedStartTime, 'endTime' => $formattedEndTime, 'createdBy' => $id])){
    echo "<script>
    alert('Slot Added!')
    </script>";
  } else {
    echo "Failed!";
  }
}
?>
</body>
</html>
