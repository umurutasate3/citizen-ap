<?php 
include "../includes/header.php";

// Fetch officer ID from the URL
$officerId = isset($_GET['userId']) ? $_GET['userId'] : null;

if (!$officerId) {
    echo "Officer ID not specified.";
    exit;
}

// Fetch officer details
$sqlOfficer = "SELECT username, email FROM users WHERE userId = :officerId";
$stmtOfficer = $pdo->prepare($sqlOfficer);
$stmtOfficer->bindParam(':officerId', $officerId);
$stmtOfficer->execute();
$officer = $stmtOfficer->fetch(PDO::FETCH_ASSOC);

// Fetch slot and appointment counts for the officer
$sqlSlots = "SELECT COUNT(*) AS slotCount FROM slots WHERE officerId = :officerId";
$sqlAppointments = "SELECT 
    COUNT(*) AS totalAppointments,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pendingAppointments,
    COUNT(CASE WHEN status = 'approved' THEN 1 END) AS approvedAppointments,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) AS completedAppointments,
    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) AS cancelledAppointments,
    COUNT(CASE WHEN startTime >= CURDATE() THEN 1 END) AS upcomingAppointments
    FROM appointments 
    INNER JOIN slots ON appointments.slotId = slots.id 
    WHERE slots.officerId = :officerId";

$stmtSlots = $pdo->prepare($sqlSlots);
$stmtSlots->bindParam(':officerId', $officerId);
$stmtSlots->execute();
$slotData = $stmtSlots->fetch(PDO::FETCH_ASSOC);

$stmtAppointments = $pdo->prepare($sqlAppointments);
$stmtAppointments->bindParam(':officerId', $officerId);
$stmtAppointments->execute();
$appData = $stmtAppointments->fetch(PDO::FETCH_ASSOC);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo htmlspecialchars($officer['username']); ?> Status</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Officer Status</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Officer Info Box -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $slotData['slotCount']; ?></h3>
                            <p>Slots Created</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-list"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Appointments Info Box -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?php echo $appData['totalAppointments']; ?></h3>
                            <p>Total Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-calendar"></i>
                        </div>
                    </div>
                </div>

                <!-- Pending Appointments Info Box -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?php echo $appData['pendingAppointments']; ?></h3>
                            <p>Pending Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-clock"></i>
                        </div>
                    </div>
                </div>

                <!-- Approved Appointments Info Box -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?php echo $appData['approvedAppointments']; ?></h3>
                            <p>Approved Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-check"></i>
                        </div>
                    </div>
                </div>

                <!-- Completed Appointments Info Box -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3><?php echo $appData['completedAppointments']; ?></h3>
                            <p>Completed Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-thumbs-up"></i>
                        </div>
                    </div>
                </div>

                <!-- Cancelled Appointments Info Box -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo $appData['cancelledAppointments']; ?></h3>
                            <p>Cancelled Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-times"></i>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Appointments Info Box -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $appData['upcomingAppointments']; ?></h3>
                            <p>Upcoming Appointments</p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <canvas id="officerChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Setup chart data
var ctx = document.getElementById('officerChart').getContext('2d');
var chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Slots', 'Pending Appointments', 'Approved Appointments', 'Completed Appointments', 'Cancelled Appointments', 'Upcoming Appointments'],
        datasets: [{
            label: 'Officer Activity',
            data: [<?php echo $slotData['slotCount']; ?>, <?php echo $appData['pendingAppointments']; ?>, <?php echo $appData['approvedAppointments']; ?>, <?php echo $appData['completedAppointments']; ?>, <?php echo $appData['cancelledAppointments']; ?>, <?php echo $appData['upcomingAppointments']; ?>],
            backgroundColor: ['#17a2b8', '#ffc107', '#28a745', '#007bff', '#dc3545', '#20c997']
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php 
include "../includes/footer.php";
?>
