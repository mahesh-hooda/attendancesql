<?php
require_once 'config.php';

if (!isStudent()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - NSUT IT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .stats-card {
            border-left: 4px solid #667eea;
        }
        .progress-custom {
            height: 25px;
        }
        .attendance-good {
            color: #28a745;
        }
        .attendance-warning {
            color: #ffc107;
        }
        .attendance-danger {
            color: #dc3545;
        }
        .subject-row {
            transition: background-color 0.2s;
        }
        .subject-row:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-user-graduate"></i> Student Portal
            </a>
            <div class="navbar-text text-white">
                Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                (<?php echo htmlspecialchars($_SESSION['roll_number']); ?>)
                <a href="logout.php" class="btn btn-outline-light btn-sm ms-3">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Overall Attendance Stats -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card stats-card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-chart-bar"></i> Overall Attendance Summary
                        </h5>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <h3 id="overallPercentage" class="mb-0">--%</h3>
                                <small class="text-muted">Overall Percentage</small>
                            </div>
                            <div class="col-md-3">
                                <h3 id="totalClasses" class="mb-0">--</h3>
                                <small class="text-muted">Total Classes</small>
                            </div>
                            <div class="col-md-3">
                                <h3 id="classesAttended" class="mb-0">--</h3>
                                <small class="text-muted">Classes Attended</small>
                            </div>
                            <div class="col-md-3">
                                <div class="progress progress-custom">
                                    <div id="overallProgressBar" class="progress-bar" role="progressbar"
                                         style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject-wise Attendance -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-book"></i> Subject-wise Attendance
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Credits</th>
                                <th class="text-center">Total Classes</th>
                                <th class="text-center">Attended</th>
                                <th class="text-center">Missed</th>
                                <th class="text-center">Percentage</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody id="subjectsTable">
                            <!-- Subject attendance data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SQL Demo Information -->
        <div class="alert alert-info mt-4" role="alert">
            <h6 class="alert-heading">
                <i class="fas fa-database"></i> SQL Demo Information
            </h6>
            <small>
                This dashboard demonstrates complex SQL queries including:
                <ul class="mb-0 mt-2">
                    <li><strong>GROUP BY with aggregates:</strong> Calculating total classes and attendance per subject</li>
                    <li><strong>CASE statements:</strong> Computing attendance percentage conditionally</li>
                    <li><strong>LEFT JOIN:</strong> Including all subjects even with no attendance records</li>
                    <li><strong>Aggregate functions:</strong> Using COUNT(), SUM(), ROUND()</li>
                </ul>
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // SQL DEMO: Load attendance data on page load
        // This triggers complex GROUP BY queries in get_student_attendance.php
        document.addEventListener('DOMContentLoaded', loadAttendanceData);

        async function loadAttendanceData() {
            try {
                const response = await fetch('api/get_student_attendance.php');
                const data = await response.json();

                if (data.success) {
                    displayOverallStats(data.overall);
                    displaySubjectAttendance(data.subjects);
                }
            } catch (error) {
                console.error('Error loading attendance:', error);
            }
        }

        function displayOverallStats(overall) {
            const percentage = parseFloat(overall.overall_percentage) || 0;

            document.getElementById('overallPercentage').textContent = percentage.toFixed(2) + '%';
            document.getElementById('totalClasses').textContent = overall.total_classes || 0;
            document.getElementById('classesAttended').textContent = overall.total_present || 0;

            const progressBar = document.getElementById('overallProgressBar');
            progressBar.style.width = percentage + '%';
            progressBar.textContent = percentage.toFixed(1) + '%';

            // Color code based on percentage
            if (percentage >= 75) {
                progressBar.classList.add('bg-success');
                document.getElementById('overallPercentage').classList.add('attendance-good');
            } else if (percentage >= 65) {
                progressBar.classList.add('bg-warning');
                document.getElementById('overallPercentage').classList.add('attendance-warning');
            } else {
                progressBar.classList.add('bg-danger');
                document.getElementById('overallPercentage').classList.add('attendance-danger');
            }
        }

        function displaySubjectAttendance(subjects) {
            const table = document.getElementById('subjectsTable');
            table.innerHTML = '';

            if (subjects.length === 0) {
                table.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No attendance records found
                        </td>
                    </tr>
                `;
                return;
            }

            subjects.forEach(subject => {
                const percentage = parseFloat(subject.attendance_percentage) || 0;
                let statusClass = 'bg-success';
                let textClass = 'attendance-good';

                if (percentage < 75) {
                    statusClass = percentage >= 65 ? 'bg-warning' : 'bg-danger';
                    textClass = percentage >= 65 ? 'attendance-warning' : 'attendance-danger';
                }

                table.innerHTML += `
                    <tr class="subject-row">
                        <td><strong>${subject.subject_code}</strong></td>
                        <td>${subject.subject_name}</td>
                        <td class="text-center">${subject.credits}</td>
                        <td class="text-center">${subject.total_classes || 0}</td>
                        <td class="text-center text-success"><strong>${subject.classes_attended || 0}</strong></td>
                        <td class="text-center text-danger"><strong>${subject.classes_missed || 0}</strong></td>
                        <td class="text-center">
                            <span class="${textClass}">
                                <strong>${percentage.toFixed(2)}%</strong>
                            </span>
                        </td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar ${statusClass}" role="progressbar"
                                     style="width: ${percentage}%"
                                     aria-valuenow="${percentage}" aria-valuemin="0" aria-valuemax="100">
                                    ${percentage.toFixed(0)}%
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
    </script>
</body>
</html>
