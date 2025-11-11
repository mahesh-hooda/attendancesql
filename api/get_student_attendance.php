<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isStudent()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$student_id = $_SESSION['user_id'];
$conn = getDbConnection();

// ===============================================
// SQL DEMO: Complex query with GROUP BY and aggregate functions
// Calculating attendance percentage for each subject
// Using COUNT, SUM, and CASE statements with PDO
// Note: SQLite requires 100.0 (not 100) for float division
// ===============================================
$sql = "SELECT
            s.subject_id,
            s.subject_code,
            s.subject_name,
            s.credits,
            COUNT(a.attendance_id) AS total_classes,
            SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS classes_attended,
            SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS classes_missed,
            CASE
                WHEN COUNT(a.attendance_id) > 0 THEN
                    ROUND((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) * 100.0 / COUNT(a.attendance_id)), 2)
                ELSE 0
            END AS attendance_percentage
        FROM subjects s
        LEFT JOIN attendance a ON s.subject_id = a.subject_id AND a.student_id = :student_id
        GROUP BY s.subject_id, s.subject_code, s.subject_name, s.credits
        ORDER BY s.subject_code";

$stmt = $conn->prepare($sql);
$stmt->execute(['student_id' => $student_id]);
$attendance_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ===============================================
// SQL DEMO: Aggregate query for overall attendance
// Calculating overall attendance across all subjects
// Using PDO with named parameters
// ===============================================
$overall_sql = "SELECT
                    COUNT(attendance_id) AS total_classes,
                    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS total_present,
                    CASE
                        WHEN COUNT(attendance_id) > 0 THEN
                            ROUND((SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) * 100.0 / COUNT(attendance_id)), 2)
                        ELSE 0
                    END AS overall_percentage
                FROM attendance
                WHERE student_id = :student_id";

$overall_stmt = $conn->prepare($overall_sql);
$overall_stmt->execute(['student_id' => $student_id]);
$overall = $overall_stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'subjects' => $attendance_data,
    'overall' => $overall
]);

$stmt = null;
$overall_stmt = null;
$conn = null;
?>
