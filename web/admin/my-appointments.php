<?php 
include "../includes/header.php";

// Get the selected status from the request, default to empty for all appointments
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$userId = isset($_GET['userId']) ? $_GET['userId'] : $id;
// Prepare the SQL query based on the selected status
$sql = "SELECT * FROM appointments 
        INNER JOIN slots ON appointments.slotId = slots.id 
        WHERE appointments.citizenId = '$userId'";

if ($statusFilter) {
    $sql .= " AND appointments.status = :status";
}

$stmt = $pdo->prepare($sql);

// Bind the parameter if a status is selected
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
                            <!-- Filter Form -->
                            <form method="GET" action="">
                                <div class="form-group">
                                    <label for="status">Filter by Status:</label>
                                    <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">All</option>
                                        <option value="pending" <?php echo ($statusFilter === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="approved" <?php echo ($statusFilter === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                        <option value="completed" <?php echo ($statusFilter === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo ($statusFilter === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                            </form>

                            <table id="example1" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>N<sup><u>o</u></sup></th>
                                        <th>Reason</th>
                                        <th>Start from</th>
                                        <th>End to</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($Appointments as $index => $appointment): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['startTime']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['endTime']); ?></td>
                                        <td class="<?php 
                                            // Determine the class based on the appointment status
                                            switch (htmlspecialchars($appointment['status'])) {
                                                case 'cancelled':
                                                    echo 'text-danger'; // Red color for cancelled
                                                    break;
                                                case 'pending':
                                                    echo 'text-warning'; // Yellow color for pending
                                                    break;
                                                case 'approved':
                                                    echo 'text-success'; // Green color for approved
                                                    break;
                                                case 'completed':
                                                    echo 'text-info'; // Blue color for completed
                                                    break;
                                                default:
                                                    echo 'text-secondary'; // Default color
                                                    break;
                                            }
                                        ?>">
                                            <?php echo htmlspecialchars($appointment['status']); ?>
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
<?php 
include "../includes/datatableFooter.php";
?>
