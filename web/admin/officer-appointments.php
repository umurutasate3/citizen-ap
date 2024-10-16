<?php 
include "../includes/header.php"; 

// Get the officer's user ID from the URL
$userId = isset($_GET['userId']) ? $_GET['userId'] : '';

// Fetch appointments for the selected officer
$sql = "SELECT * FROM appointments 
        INNER JOIN slots ON appointments.slotId = slots.id 
        INNER JOIN users ON users.userId = appointments.citizenId 
        WHERE slots.officerId = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $userId);
$stmt->execute();
$appointments = $stmt->fetchAll();
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Appointments for Officer</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Officer Appointments</li>
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
                  <?php foreach ($appointments as $index => $appointment): ?>
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
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php  
include "../includes/footer.php";
?>
