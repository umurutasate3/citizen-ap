<?php
include "../../includes/conn.php";

if (isset($_GET['userId'])) {
    $userId = intval($_GET['userId']);
    
    try {
        $pdo->beginTransaction();
        $sql_users = "UPDATE users SET isActive ='0' WHERE userId = ?";
        $stmt_users = $pdo->prepare($sql_users);
        $stmt_users->execute([$userId]);
        $pdo->commit();

        if ($stmt_users->rowCount() > 0) {
            header("Location: ../");
            exit;
        } else {
            header("Location: ../");
            exit;
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        header("Location: ../index.php?error=Failed to delete records: " . $e->getMessage());
        exit;
    }
} else {
    header("Location: ../index.php?error=No userId specified");
    exit;
}
?>
