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
// PDO uses beginTransaction instead of begin_transaction
$conn->beginTransaction();

try {
    // ===============================================
    // SQL DEMO: DELETE query
    // Removing existing attendance records for the date
    // This allows teachers to update attendance if needed
    // ===============================================
    $delete_sql = "DELETE FROM attendance WHERE subject_id = :subject_id AND date = :date";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->execute(['subject_id' => $subject_id, 'date' => $date]);

    // ===============================================
    // SQL DEMO: INSERT query in a loop
    // Bulk inserting attendance records for multiple students
    // Using PDO with named parameters
    // ===============================================
    $insert_sql = "INSERT INTO attendance (student_id, subject_id, teacher_id, date, status)
                   VALUES (:student_id, :subject_id, :teacher_id, :date, :status)";
    $insert_stmt = $conn->prepare($insert_sql);

    foreach ($attendance as $record) {
        $student_id = $record['student_id'];
        $status = $record['status'];

        $insert_stmt->execute([
            'student_id' => $student_id,
            'subject_id' => $subject_id,
            'teacher_id' => $teacher_id,
            'date' => $date,
            'status' => $status
        ]);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Attendance marked successfully',
        'records_inserted' => count($attendance)
    ]);

} catch (Exception $e) {
    // Rollback on error
    $conn->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error marking attendance: ' . $e->getMessage()
    ]);
}

$conn = null;
?>
