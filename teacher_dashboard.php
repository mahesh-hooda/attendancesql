<?php
require_once 'config.php';

if (!isTeacher()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - NSUT IT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .subject-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .subject-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .attendance-checkbox {
            transform: scale(1.5);
            margin-right: 10px;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-chalkboard-teacher"></i> Teacher Portal
            </a>
            <div class="navbar-text text-white">
                Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                <a href="logout.php" class="btn btn-outline-light btn-sm ms-3">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Subject Selection -->
        <div id="subjectSelection">
            <h4 class="mb-4">Your Subjects</h4>
            <div class="row" id="subjectsList">
                <!-- Subjects will be loaded here -->
            </div>
        </div>

        <!-- Attendance Marking Section -->
        <div id="attendanceSection" class="d-none">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-secondary" onclick="backToSubjects()">
                    <i class="fas fa-arrow-left"></i> Back to Subjects
                </button>
                <h4 id="currentSubject" class="mb-0"></h4>
                <div>
                    <input type="date" id="attendanceDate" class="form-control"
                           value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Roll No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th class="text-center">Present</th>
                                    <th class="text-center">Absent</th>
                                </tr>
                            </thead>
                            <tbody id="studentsList">
                                <!-- Students will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-3">
                        <button class="btn btn-primary btn-lg" onclick="saveAttendance()">
                            <i class="fas fa-save"></i> Save Attendance
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentSubjectId = null;
        let currentSubjectName = null;

        // Load subjects on page load
        // SQL DEMO: This triggers a JOIN query in get_teacher_subjects.php
        document.addEventListener('DOMContentLoaded', loadSubjects);

        async function loadSubjects() {
            try {
                const response = await fetch('api/get_teacher_subjects.php');
                const data = await response.json();

                if (data.success) {
                    displaySubjects(data.subjects);
                }
            } catch (error) {
                console.error('Error loading subjects:', error);
            }
        }

        function displaySubjects(subjects) {
            const subjectsList = document.getElementById('subjectsList');
            subjectsList.innerHTML = '';

            subjects.forEach(subject => {
                subjectsList.innerHTML += `
                    <div class="col-md-4 mb-3">
                        <div class="card subject-card" onclick="selectSubject(${subject.subject_id}, '${subject.subject_name}')">
                            <div class="card-body">
                                <h5 class="card-title">${subject.subject_code}</h5>
                                <p class="card-text">${subject.subject_name}</p>
                                <small class="text-muted">
                                    Semester: ${subject.semester} | Credits: ${subject.credits}
                                </small>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        // SQL DEMO: This function triggers a LEFT JOIN query in get_students.php
        async function selectSubject(subjectId, subjectName) {
            currentSubjectId = subjectId;
            currentSubjectName = subjectName;

            document.getElementById('subjectSelection').classList.add('d-none');
            document.getElementById('attendanceSection').classList.remove('d-none');
            document.getElementById('currentSubject').textContent = subjectName;

            await loadStudents();
        }

        async function loadStudents() {
            const date = document.getElementById('attendanceDate').value;

            try {
                const response = await fetch(`api/get_students.php?subject_id=${currentSubjectId}&date=${date}`);
                const data = await response.json();

                if (data.success) {
                    displayStudents(data.students);
                }
            } catch (error) {
                console.error('Error loading students:', error);
            }
        }

        function displayStudents(students) {
            const studentsList = document.getElementById('studentsList');
            studentsList.innerHTML = '';

            students.forEach(student => {
                const isPresent = student.status === 'Present';
                const isAbsent = student.status === 'Absent';

                studentsList.innerHTML += `
                    <tr>
                        <td>${student.roll_number}</td>
                        <td>${student.name}</td>
                        <td>${student.email}</td>
                        <td class="text-center">
                            <input type="radio" name="attendance_${student.student_id}"
                                   value="Present" class="attendance-checkbox"
                                   ${isPresent ? 'checked' : ''}>
                        </td>
                        <td class="text-center">
                            <input type="radio" name="attendance_${student.student_id}"
                                   value="Absent" class="attendance-checkbox"
                                   ${isAbsent ? 'checked' : ''}>
                        </td>
                    </tr>
                `;
            });
        }

        // SQL DEMO: This function triggers DELETE and INSERT queries in mark_attendance.php
        async function saveAttendance() {
            const date = document.getElementById('attendanceDate').value;
            const attendance = [];

            const radios = document.querySelectorAll('input[type="radio"]:checked');
            radios.forEach(radio => {
                const studentId = radio.name.replace('attendance_', '');
                attendance.push({
                    student_id: parseInt(studentId),
                    status: radio.value
                });
            });

            if (attendance.length === 0) {
                alert('Please mark attendance for at least one student');
                return;
            }

            try {
                const response = await fetch('api/mark_attendance.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        subject_id: currentSubjectId,
                        date: date,
                        attendance: attendance
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert(`Attendance saved successfully! ${data.records_inserted} records saved.`);
                } else {
                    alert('Error saving attendance: ' + data.message);
                }
            } catch (error) {
                alert('Error saving attendance. Please try again.');
                console.error(error);
            }
        }

        function backToSubjects() {
            document.getElementById('attendanceSection').classList.add('d-none');
            document.getElementById('subjectSelection').classList.remove('d-none');
        }

        // Reload students when date changes
        document.getElementById('attendanceDate').addEventListener('change', loadStudents);
    </script>
</body>
</html>
