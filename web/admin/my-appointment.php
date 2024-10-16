<?php 
include "../includes/header.php"; // Assuming this file includes your database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

// Prepare the SQL query based on the selected status
$sql = "SELECT * FROM appointments 
        INNER JOIN slots ON appointments.slotId = slots.id 
        INNER JOIN users ON users.userId = appointments.citizenId 
        WHERE slots.officerId = :id";

if ($statusFilter) {
    $sql .= " AND appointments.status = :status";
}
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id);
if ($statusFilter) {
    $stmt->bindParam(':status', $statusFilter);
}
$stmt->execute();
$Appointments = $stmt->fetchAll();
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Appointments</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
            <li class="breadcrumb-item active">Manage Appointments</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <table id="example1" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>N<sup><u>o</u></sup></th>
                    <th>Citizen</th>
                    <th>Village</th>
                    <th>Reason</th>
                    <th>Start from</th>
                    <th>End to</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($Appointments as $index => $appointment): ?>
                  <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($appointment['username']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['village']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['startTime']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['endTime']); ?></td>
                    <td><?php echo htmlspecialchars($appointment['status']); ?></td>
                    <td>
                      <!-- Dropdown Menu -->
                      <div class="dropdown">
                        <a href="#" class="" data-toggle="dropdown">...</a>
                        <div class="dropdown-menu">
                          <!-- Pass appId to the modal using data-id -->
                          <a class="dropdown-item text-primary" href="#" data-toggle="modal" data-target="#statusModal" data-id="<?php echo $appointment['appId']; ?>">Change status</a>
                        </div>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Bootstrap Modal for Changing Status -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="statusModalLabel">Change Appointment Status</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="" method="POST">
        <div class="modal-body">
          <!-- Hidden input to store the appointment ID -->
          <input type="hidden" name="appId" id="modalAppId">
          <div class="form-group">
            <label for="status">Select Status:</label>
            <select name="status" id="status" class="form-control">
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php 
// Include footer
include "../includes/datatableFooter.php";

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appId = $_POST['appId'];
    $newStatus = $_POST['status'];

    // Update the appointment status in the database
    $sql = "UPDATE appointments SET status = :status WHERE appId = :appId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['status' => $newStatus, 'appId' => $appId]);

    // Fetch the email of the user
    $sqlEmail = "SELECT * FROM users INNER JOIN appointments inner join slots ON users.userId = appointments.citizenId AND appointments.slotId = slots.id WHERE appointments.appId = :appId";
    $stmtEmail = $pdo->prepare($sqlEmail);
    $stmtEmail->execute(['appId' => $appId]);
    $appointmentDetails = $stmtEmail->fetch(PDO::FETCH_ASSOC);
    $userEmail = $appointmentDetails['email'];
    $village = $appointmentDetails['village'];
    $reason = $appointmentDetails['reason'];
    $startTime = $appointmentDetails['startTime'];
    $endTime = $appointmentDetails['endTime'];
    // Send notification email
    require '../vendor/autoload.php'; // Include PHPMailer autoload file
    $mail = new PHPMailer();
    $mail->CharSet = "utf-8";
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->Username = "paccyhabi@gmail.com"; // Your email
    $mail->Password = "xzec mayt wrwu ewch"; // Your email password
    $mail->SMTPSecure = "ssl";
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 465;
    $mail->From = "paccyhabi@gmail.com"; // Your email
    $mail->FromName = 'Citizen Appointment System';
    $mail->addAddress($userEmail); // User's email
    $mail->Subject = 'Appointment Status Updated';
    $mail->isHTML(true);
    $mail->Body = "
        <p>Your appointment status is <strong>$newStatus</strong></p>
        <p><strong>Appointment Details:</strong></p>
        <p><strong>Reason:</strong>$reason</p>
        <p><strong>Village:</strong> $village</p>
        <p><strong>Start Time:</strong>$startTime</p>
        <p><strong>End Time:</strong>$endTime</p>
        <p>Please keep checking your emails for further updates.</p>
    ";

    // Send the email
    if ($mail->send()) {
        echo "Email notification sent successfully.";
    } else {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }

    // Redirect back to the manageAppointments page
    echo "<script>window.location.href='my-appointment.php';</script>";
}
?>

<!-- JavaScript to pass the appId to the modal -->
<script>
  $('#statusModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var appId = button.data('id'); // Extract info from data-id attribute

    // Update the modal's hidden input with the appointment ID
    var modal = $(this);
    modal.find('#modalAppId').val(appId);
  });
</script>
