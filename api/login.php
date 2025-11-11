<?php
require_once '../config.php';

header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$userType = $data['userType'] ?? '';

if (empty($email) || empty($password) || empty($userType)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$conn = getDbConnection();

// ===============================================
// SQL DEMO: SELECT query with WHERE clause
// Authenticating user based on email and password
// Using PDO prepared statements for security
// ===============================================
if ($userType === 'student') {
    $sql = "SELECT student_id, roll_number, name, email FROM students WHERE email = :email AND password = :password";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['email' => $email, 'password' => $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['student_id'];
        $_SESSION['user_type'] = 'student';
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['roll_number'] = $user['roll_number'];

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'redirect' => 'student_dashboard.php'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} else if ($userType === 'teacher') {
    // ===============================================
    // SQL DEMO: SELECT query with prepared statements
    // Preventing SQL injection using parameterized queries with PDO
    // ===============================================
    $sql = "SELECT teacher_id, name, email, department FROM teachers WHERE email = :email AND password = :password";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['email' => $email, 'password' => $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['teacher_id'];
        $_SESSION['user_type'] = 'teacher';
        $_SESSION['user_name'] = $user['name'];

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'redirect' => 'teacher_dashboard.php'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid user type']);
}

$stmt = null;
$conn = null;
?>
