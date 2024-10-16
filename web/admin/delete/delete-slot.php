<?php
include "../../includes/conn.php";

if (isset($_GET['slotId'])) {
    $slotId = intval($_GET['slotId']);
    
    try {
        $pdo->beginTransaction();
        $sql_slots = "DELETE FROM slots WHERE id = ?";
        $stmt_slots = $pdo->prepare($sql_slots);
        $stmt_slots->execute([$slotId]);
        $pdo->commit();

        if ($stmt_slots->rowCount() > 0) {
            header("Location: ../my-slots.php");
            exit;
        } else {
            header("Location: ../my-slots.php");
            exit;
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        header("Location: ../my-slots.php?error=Failed to delete records: " . $e->getMessage());
        exit;
    }
} else {
    header("Location: ../my-slots.php?error=No slotId specified");
    exit;
}
?>
