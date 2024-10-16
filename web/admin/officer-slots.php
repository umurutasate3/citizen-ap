<?php 
include "../includes/header.php";
$userId = isset($_GET['userId']) ? $_GET['userId'] : '';
// Fetch Slots and their corresponding department names from the database
$now = date('Y-m-d H:i:s'); // Ensure the time format is correct
$sql = "SELECT * from slots where availability='1' and startTime>='$now' and officerId = '$userId'";
$stmt = $pdo->query($sql);
$Slots = $stmt->fetchAll();

// Handle form submission to update the slot
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $slotId = $_POST['slotId'];
  $startTime = $_POST['startTime'];
  $endTime = $_POST['endTime'];
  $availability = $_POST['availability'];

  // Update the slot in the database
  $updateSql = "UPDATE slots SET startTime = ?, endTime = ?, availability = ? WHERE id = ?";
  $stmt = $pdo->prepare($updateSql);
  $stmt->execute([$startTime, $endTime, $availability, $slotId]);

  // Use JavaScript to redirect after submission
  echo '<script>window.onload = function() { alert("Slot updated successfully."); window.location.href="manage-slots.php"; };</script>';
  exit();
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Slots</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Manage Slots</li>
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
                    <th>Start time</th>
                    <th>End time</th>
                    <th>Appointment duration</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($Slots as $index => $Slot): 
                    // Calculate the duration between start time and end time
                    $startTime = new DateTime($Slot['startTime']);
                    $endTime = new DateTime($Slot['endTime']);
                    $duration = $startTime->diff($endTime);
                    ?>
                    <tr>
                      <td><?php echo $index + 1; ?></td>
                      <td><?php echo htmlspecialchars($Slot['startTime']); ?></td>
                      <td><?php echo htmlspecialchars($Slot['endTime']); ?></td>
                      <td>
                        <?php 
                        // Display the duration in hours and minutes format
                        echo $duration->format('%h hours %i minutes');
                        ?>
                      </td>
                      <td>
                        <!-- Dropdown Menu -->
                        <div class="dropdown">
                          <a href="#" class="" data-toggle="dropdown">...</a>
                          <div class="dropdown-menu">
                            <a class="dropdown-item text-primary" href="#" data-toggle="modal" data-target="#editSlotModal" data-id="<?php echo $Slot['id']; ?>" data-start="<?php echo $Slot['startTime']; ?>" data-end="<?php echo $Slot['endTime']; ?>" data-availability="<?php echo $Slot['availability']; ?>">Edit</a>
                            <a class="dropdown-item text-primary" href="./delete/delete-slot.php?slotId=<?php echo $Slot['id']; ?>" 
                               onclick="return confirmDelete();">Delete</a>
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
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Modal for Editing Slot -->
<div class="modal fade" id="editSlotModal" tabindex="-1" role="dialog" aria-labelledby="editSlotModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editSlotModalLabel">Edit Slot</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" id="editSlotForm">
        <div class="modal-body">
          <!-- Hidden input to store the slot ID -->
          <input type="hidden" name="slotId" id="slotId">

          <!-- Start Time -->
          <div class="form-group">
            <label for="startTime">Start Time</label>
            <input type="datetime-local" name="startTime" id="startTime" class="form-control" required>
          </div>

          <!-- End Time -->
          <div class="form-group">
            <label for="endTime">End Time</label>
            <input type="datetime-local" name="endTime" id="endTime" class="form-control" required>
          </div>

          <!-- Availability -->
          <div class="form-group">
            <label for="availability">Availability</label>
            <select name="availability" id="availability" class="form-control" required>
              <option value="1">Available</option>
              <option value="0">Unavailable</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this slot? This action cannot be undone.');
}

// Populate the modal with slot data
$('#editSlotModal').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget); // Button that triggered the modal
  var slotId = button.data('id'); // Extract slot ID from data-* attributes
  var startTime = button.data('start'); // Extract startTime
  var endTime = button.data('end'); // Extract endTime
  var availability = button.data('availability'); // Extract availability

  // Update the modal's fields with the slot data
  var modal = $(this);
  modal.find('#slotId').val(slotId);
  modal.find('#startTime').val(startTime.replace(' ', 'T')); // Format for datetime-local input
  modal.find('#endTime').val(endTime.replace(' ', 'T'));
  modal.find('#availability').val(availability);
});

// Redirect after form submission
$('#editSlotForm').on('submit', function(e) {
  e.preventDefault(); // Prevent the default form submission
  var form = $(this);
  $.ajax({
    type: form.attr('method'),
    url: form.attr('action'),
    data: form.serialize(),
    success: function(response) {
      alert("Slot updated successfully.");
      window.location.href = "my-slots.php"; // Redirect to manage-slots.php
    },
    error: function() {
      alert("An error occurred while updating the slot.");
    }
  });
});
</script>

<?php  
 include "../includes/datatableFooter.php";
?>  
