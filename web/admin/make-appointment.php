<?php 
include "../includes/header.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = ''; // Initialize message variable

if (isset($_POST['Make'])) {
    $village = $_POST['village'];
    $reason = $_POST['reason'];
    $slotId = $_POST['slot'];

    // Prepare SQL statement to insert appointment
    $sql = "INSERT INTO appointments (village, reason, citizenId, slotId) VALUES (:village, :reason, :by, :slot)";
    $stmt = $pdo->prepare($sql);
    $params = [
        ':village' => $village,
        ':reason' => $reason,
        ':slot' => $slotId,
        ':by' => $id
    ];
    
    // Execute the statement and handle success or failure
    if ($stmt->execute($params)) {
        // Appointment made successfully
        $message = 'Appointment Made!';

        // Fetch user's email for the confirmation
        $sqlUser = "SELECT email FROM users WHERE userId = :id"; // Assuming you have the user's email in the users table
        $stmtUser = $pdo->prepare($sqlUser);
        $stmtUser->execute([':id' => $id]);
        $user = $stmtUser->fetch();

        // Fetch slot details for the email
        $sqlSlot = "SELECT startTime, endTime FROM slots WHERE id = :slotId";
        $stmtSlot = $pdo->prepare($sqlSlot);
        $stmtSlot->execute([':slotId' => $slotId]);
        $slot = $stmtSlot->fetch();

        if ($user && $slot) {
            // Send confirmation email to the user
            require '../vendor/autoload.php'; // Include PHPMailer autoload file
            $mail = new PHPMailer();
            $mail->CharSet = "utf-8";
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Username = "paccyhabi@gmail.com"; // Your email address
            $mail->Password = "xzec mayt wrwu ewch"; // Your email password
            $mail->SMTPSecure = "ssl";
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465;
            $mail->From = "paccyhabi@gmail.com"; // Your email address
            $mail->FromName = 'Citizen Appointment System'; 
            $mail->addAddress($user['email']); // User's email
            $mail->Subject = 'Appointment Confirmation';
            $mail->isHTML(true);
            $mail->Body = "
                  <p>Your appointment has been successfully made!</p>
                  <p><strong>Village:</strong> $village</p>
                  <p><strong>Reason:</strong> $reason</p>
                  <p><strong>Slot Details:</strong></p>
                  <p><strong>Start Time:</strong> " . htmlspecialchars($slot['startTime']) . "</p>
                  <p><strong>End Time:</strong> " . htmlspecialchars($slot['endTime']) . "</p>
                  <p>Please wait for your appointment to be approved. Make sure to keep checking your email for updates.</p>
              ";

            // Send the email
            if (!$mail->Send()) {
                $message .= '<div class="alert alert-danger">Mail Error: ' . $mail->ErrorInfo . '</div>';
            }
        }
    } else {
        $message = 'Unable to Make Appointment';
    }
}

// Get current time in correct format
$now = date('Y-m-d H:i:s');
$sql2 = "SELECT * FROM slots INNER JOIN users ON users.userId = slots.officerId WHERE availability='1' AND startTime >= :now";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute([':now' => $now]);
$slots = $stmt2->fetchAll(); 
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="ml-3 text-dark">Make Appointment</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active">Make Appointment</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row px-4">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Appointment Details</h3>
                        </div>
                        <form role="form" method="POST">
                            <div class="card-body">
                                <!-- Display success or error message if available -->
                                <?php if (!empty($message)): ?>
                                    <div class="alert alert-<?php echo strpos($message, 'Unable') !== false ? 'danger' : 'success'; ?>">
                                        <?= htmlspecialchars($message) ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Village input field -->
                                <div class="form-group">
                                    <label for="village">Village</label>
                                    <input type="text" id="village" class="form-control" placeholder="Enter your village" required name="village">
                                </div>

                                <!-- Reason input field -->
                                <div class="form-group">
                                    <label for="reason">Reason</label>
                                    <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Enter the reason for the appointment" required></textarea>
                                </div>

                                <!-- Slot selection field -->
                                <div class="form-group">
                                    <label for="slot">Slot</label>
                                    <select id="slot" class="form-control" name="slot" required>
                                        <?php foreach ($slots as $slot): ?>
                                            <option value="<?= htmlspecialchars($slot['id']); ?>">
                                                <?= htmlspecialchars($slot['startTime']); ?> - <?= htmlspecialchars($slot['endTime']); ?> BY <?= htmlspecialchars($slot['username']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <!-- Submit button -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary" name="Make">Make Appointment</button>
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
