<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSUT IT Attendance Portal - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        .btn-custom:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card">
                    <div class="card-header text-center py-4">
                        <h3><i class="fas fa-graduation-cap"></i> NSUT IT Branch</h3>
                        <p class="mb-0">Attendance Management Portal</p>
                    </div>
                    <div class="card-body p-5">
                        <div class="mb-4 text-center">
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="userType" id="studentRadio" value="student" checked>
                                <label class="btn btn-outline-primary" for="studentRadio">
                                    <i class="fas fa-user-graduate"></i> Student
                                </label>

                                <input type="radio" class="btn-check" name="userType" id="teacherRadio" value="teacher">
                                <label class="btn btn-outline-primary" for="teacherRadio">
                                    <i class="fas fa-chalkboard-teacher"></i> Teacher
                                </label>
                            </div>
                        </div>

                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" required
                                           placeholder="Enter your email">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" required
                                           placeholder="Enter your password">
                                </div>
                            </div>

                            <div class="alert alert-info" role="alert">
                                <small>
                                    <strong>Demo Credentials:</strong><br>
                                    <span id="demoCredentials">
                                        Student: aarav.sharma@nsut.ac.in / pass001<br>
                                        Roll: 2021IT001-2021IT020 / pass001-pass020
                                    </span>
                                </small>
                            </div>

                            <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>

                            <button type="submit" class="btn btn-custom w-100 py-2">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update demo credentials based on user type
        document.querySelectorAll('input[name="userType"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const demoCredentials = document.getElementById('demoCredentials');
                if (this.value === 'student') {
                    demoCredentials.innerHTML = `Student: aarav.sharma@nsut.ac.in / pass001<br>
                                                Roll: 2021IT001-2021IT020 / pass001-pass020`;
                } else {
                    demoCredentials.innerHTML = `Teacher: rajesh.kumar@nsut.ac.in / teacher001<br>
                                               Teachers: teacher001-teacher005`;
                }
            });
        });

        // Handle login form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const userType = document.querySelector('input[name="userType"]:checked').value;
            const errorAlert = document.getElementById('errorAlert');

            errorAlert.classList.add('d-none');

            try {
                // SQL DEMO: API call that triggers SELECT query in login.php
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email, password, userType })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    errorAlert.textContent = data.message;
                    errorAlert.classList.remove('d-none');
                }
            } catch (error) {
                errorAlert.textContent = 'An error occurred. Please try again.';
                errorAlert.classList.remove('d-none');
            }
        });
    </script>
</body>
</html>
