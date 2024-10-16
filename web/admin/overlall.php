<?php 
include "../includes/header.php"; // Include your header

// Fetch overall data
// Fetch total number of slots, appointments, pending appointments, and completed appointments for all officers
$sqlTotalSlots = "SELECT COUNT(*) AS totalSlots FROM slots";
$sqlTotalAppointments = "SELECT COUNT(*) AS totalAppointments FROM appointments";
$sqlPendingAppointments = "SELECT COUNT(*) AS pendingAppointments FROM appointments WHERE status = 'pending'";
$sqlCompletedAppointments = "SELECT COUNT(*) AS completedAppointments FROM appointments WHERE status = 'completed'";

// Assuming you already have a PDO instance ($pdo) for your database connection
$totalSlots = $pdo->query($sqlTotalSlots)->fetch(PDO::FETCH_ASSOC)['totalSlots'];
$totalAppointments = $pdo->query($sqlTotalAppointments)->fetch(PDO::FETCH_ASSOC)['totalAppointments'];
$pendingAppointments = $pdo->query($sqlPendingAppointments)->fetch(PDO::FETCH_ASSOC)['pendingAppointments'];
$completedAppointments = $pdo->query($sqlCompletedAppointments)->fetch(PDO::FETCH_ASSOC)['completedAppointments'];
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Overall Officers Report</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Overall Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Line Chart for Appointments -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Appointments Overview</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="appointmentsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Pie Chart for Appointments Status -->
                <div class="col-md-6">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Appointments Status</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="statusPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data for Line Chart
    const appointmentsChartCtx = document.getElementById('appointmentsChart').getContext('2d');
    const appointmentsChart = new Chart(appointmentsChartCtx, {
        type: 'line',
        data: {
            labels: ['Total Slots', 'Total Appointments', 'Pending Appointments', 'Completed Appointments'],
            datasets: [{
                label: 'Appointments Overview',
                data: [<?php echo $totalSlots; ?>, <?php echo $totalAppointments; ?>, <?php echo $pendingAppointments; ?>, <?php echo $completedAppointments; ?>],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        }
    });

    // Data for Pie Chart
    const statusPieChartCtx = document.getElementById('statusPieChart').getContext('2d');
    const statusPieChart = new Chart(statusPieChartCtx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'Completed'],
            datasets: [{
                label: 'Appointment Status',
                data: [<?php echo $pendingAppointments; ?>, <?php echo $completedAppointments; ?>],
                backgroundColor: [
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        }
    });
</script>

<?php
include "../includes/footer.php"; // Include your footer
?>
