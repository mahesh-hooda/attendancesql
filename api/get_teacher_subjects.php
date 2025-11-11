<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isTeacher()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$teacher_id = $_SESSION['user_id'];
$conn = getDbConnection();

// ===============================================
// SQL DEMO: JOIN query
// Fetching subjects assigned to a teacher using INNER JOIN
// ===============================================
$sql = "SELECT s.subject_id, s.subject_code, s.subject_name, s.semester, s.credits
        FROM subjects s
        INNER JOIN subject_teacher_mapping stm ON s.subject_id = stm.subject_id
        WHERE stm.teacher_id = ?
        ORDER BY s.subject_code";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

echo json_encode(['success' => true, 'subjects' => $subjects]);

$stmt->close();
$conn->close();
?>
