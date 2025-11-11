<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isTeacher()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$subject_id = $data['subject_id'] ?? '';
$date = $data['date'] ?? '';
$attendance = $data['attendance'] ?? [];

if (empty($subject_id) || empty($date) || empty($attendance)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$teacher_id = $_SESSION['user_id'];
$conn = getDbConnection();

// Start transaction for data consistency
$conn->begin_transaction();

try {
    // ===============================================
    // SQL DEMO: DELETE query
    // Removing existing attendance records for the date
    // This allows teachers to update attendance if needed
    // ===============================================
    $delete_sql = "DELETE FROM attendance WHERE subject_id = ? AND date = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("is", $subject_id, $date);
    $delete_stmt->execute();
    $delete_stmt->close();

    // ===============================================
    // SQL DEMO: INSERT query in a loop
    // Bulk inserting attendance records for multiple students
    // ===============================================
    $insert_sql = "INSERT INTO attendance (student_id, subject_id, teacher_id, date, status)
                   VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);

    foreach ($attendance as $record) {
        $student_id = $record['student_id'];
        $status = $record['status'];

        $insert_stmt->bind_param("iiiss", $student_id, $subject_id, $teacher_id, $date, $status);
        $insert_stmt->execute();
    }

    $insert_stmt->close();

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Attendance marked successfully',
        'records_inserted' => count($attendance)
    ]);

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Error marking attendance: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
