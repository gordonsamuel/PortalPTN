<?php
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $status = $_POST['status'];

    $sql = "UPDATE students SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $student_id]);

    header("Location: admin_dashboard.php");
}
?>
