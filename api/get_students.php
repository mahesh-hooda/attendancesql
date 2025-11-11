<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isTeacher()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$subject_id = $_GET['subject_id'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');

if (empty($subject_id)) {
    echo json_encode(['success' => false, 'message' => 'Subject ID is required']);
    exit;
}

$conn = getDbConnection();

// ===============================================
// SQL DEMO: LEFT JOIN query
// Fetching all students with their attendance status for a specific date
// Using LEFT JOIN to include students even if attendance not marked
// ===============================================
$sql = "SELECT
            s.student_id,
            s.roll_number,
            s.name,
            s.email,
            a.attendance_id,
            a.status,
            a.date
        FROM students s
        LEFT JOIN attendance a ON s.student_id = a.student_id
            AND a.subject_id = ?
            AND a.date = ?
        ORDER BY s.roll_number";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $subject_id, $date);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode([
    'success' => true,
    'students' => $students,
    'date' => $date
]);

$stmt->close();
$conn->close();
?>
