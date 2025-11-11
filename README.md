# NSUT IT Branch - Teacher Attendance Portal

A lightweight, Bootstrap-based attendance management system for demonstrating SQL operations in a real-world application.

## Features

- **Teacher Portal**: Mark attendance for students by subject and date
- **Student Portal**: View attendance records and percentage for each subject
- **SQL Demonstrations**: Extensive use of SQL queries with detailed comments
- **Bootstrap UI**: Responsive, modern interface
- **Mock Data**: Pre-populated with 20 students, 5 teachers, and 5 subjects

## SQL Concepts Demonstrated

This project showcases various SQL operations:

1. **CREATE TABLE** - with primary keys, foreign keys, and constraints
2. **INSERT** - single and bulk inserts with subqueries
3. **SELECT** - with WHERE, JOIN, LEFT JOIN, GROUP BY
4. **UPDATE** - (via DELETE + INSERT pattern for attendance)
5. **DELETE** - removing existing attendance records
6. **Aggregate Functions** - COUNT(), SUM(), ROUND()
7. **CASE Statements** - conditional logic in queries
8. **GROUP BY** - with multiple aggregates
9. **Prepared Statements** - preventing SQL injection
10. **Transactions** - ensuring data consistency
11. **CREATE VIEW** - for attendance summaries
12. **Complex JOINs** - multiple table relationships

## Project Structure

```
attendancesql/
├── index.php                    # Login page (both student & teacher)
├── teacher_dashboard.php        # Teacher interface for marking attendance
├── student_dashboard.php        # Student interface for viewing attendance
├── config.php                   # Database configuration & helper functions
├── logout.php                   # Session cleanup
├── database.sql                 # Complete database schema with mock data
├── .htaccess                    # Apache configuration
├── api/
│   ├── login.php               # Authentication API (SQL SELECT demo)
│   ├── get_teacher_subjects.php # Fetch teacher's subjects (JOIN demo)
│   ├── get_students.php        # Fetch students with attendance (LEFT JOIN demo)
│   ├── mark_attendance.php     # Save attendance records (INSERT/DELETE demo)
│   └── get_student_attendance.php # Attendance statistics (GROUP BY demo)
└── README.md                    # This file
```

## Setup Instructions

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB)
- Apache/Nginx web server
- phpMyAdmin (optional, for database management)

### Installation Steps

#### 1. Clone/Download the Project

```bash
git clone <repository-url>
cd attendancesql
```

#### 2. Create Database

Open phpMyAdmin or MySQL CLI and execute:

```sql
CREATE DATABASE nsut_attendance;
```

#### 3. Import Database Schema

**Option A: Using phpMyAdmin**
- Open phpMyAdmin
- Select `nsut_attendance` database
- Click "Import" tab
- Choose `database.sql` file
- Click "Go"

**Option B: Using MySQL CLI**

```bash
mysql -u root -p nsut_attendance < database.sql
```

#### 4. Configure Database Connection

Edit `config.php` and update database credentials if needed:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Your MySQL password
define('DB_NAME', 'nsut_attendance');
```

#### 5. Set Up Web Server

**For XAMPP/WAMP:**
- Copy project folder to `htdocs/` or `www/`
- Access: `http://localhost/attendancesql/`

**For PHP Built-in Server (Development):**

```bash
cd attendancesql
php -S localhost:8000
```

Then open: `http://localhost:8000`

#### 6. Access the Application

Open your browser and navigate to the application URL.

## Demo Credentials

### Teacher Accounts

| Name | Email | Password |
|------|-------|----------|
| Dr. Rajesh Kumar | rajesh.kumar@nsut.ac.in | teacher001 |
| Dr. Priya Sharma | priya.sharma@nsut.ac.in | teacher002 |
| Dr. Amit Verma | amit.verma@nsut.ac.in | teacher003 |
| Dr. Sunita Rao | sunita.rao@nsut.ac.in | teacher004 |
| Dr. Vikram Singh | vikram.singh@nsut.ac.in | teacher005 |

### Student Accounts

All students follow the pattern:
- **Email**: `<firstname>.<lastname>@nsut.ac.in`
- **Password**: `pass001` to `pass020`

| Roll Number | Name | Email | Password |
|-------------|------|-------|----------|
| 2021IT001 | Aarav Sharma | aarav.sharma@nsut.ac.in | pass001 |
| 2021IT002 | Vivaan Gupta | vivaan.gupta@nsut.ac.in | pass002 |
| 2021IT003 | Aditya Kumar | aditya.kumar@nsut.ac.in | pass003 |
| ... | ... | ... | ... |
| 2021IT020 | Isha Bhatia | isha.bhatia@nsut.ac.in | pass020 |

*Full list available in `database.sql`*

### Subjects & Teacher Mapping

| Subject Code | Subject Name | Teacher | Semester |
|--------------|--------------|---------|----------|
| IT301 | Database Management Systems | Dr. Rajesh Kumar | 5 |
| IT302 | Operating Systems | Dr. Priya Sharma | 5 |
| IT303 | Computer Networks | Dr. Amit Verma | 5 |
| IT304 | Software Engineering | Dr. Sunita Rao | 5 |
| IT305 | Web Technologies | Dr. Vikram Singh | 5 |

## Usage Guide

### For Teachers

1. **Login**: Use teacher credentials on the main page
2. **Select Subject**: Choose the subject for which you want to mark attendance
3. **Select Date**: Choose the date (defaults to today)
4. **Mark Attendance**: Select Present/Absent for each student
5. **Save**: Click "Save Attendance" button

**SQL Query Executed**: When marking attendance, the system:
- Deletes existing attendance for that date (allows updates)
- Inserts new attendance records for all students

### For Students

1. **Login**: Use student credentials on the main page
2. **View Dashboard**: See overall and subject-wise attendance
3. **Track Progress**: Monitor attendance percentage for each subject

**SQL Query Executed**: When viewing attendance, the system:
- Groups attendance by subject
- Calculates total classes, attended, and percentage
- Uses aggregate functions (COUNT, SUM, CASE)

## SQL Query Examples

All SQL operations are documented with comments in the code. Here are some key queries:

### 1. Fetch Student Attendance Summary (GROUP BY)

```sql
-- Location: api/get_student_attendance.php
SELECT
    s.subject_id,
    s.subject_code,
    s.subject_name,
    COUNT(a.attendance_id) AS total_classes,
    SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS classes_attended,
    ROUND((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.attendance_id)) * 100, 2) AS attendance_percentage
FROM subjects s
LEFT JOIN attendance a ON s.subject_id = a.subject_id AND a.student_id = ?
GROUP BY s.subject_id
```

### 2. Fetch Students with Attendance Status (LEFT JOIN)

```sql
-- Location: api/get_students.php
SELECT s.student_id, s.roll_number, s.name, a.status
FROM students s
LEFT JOIN attendance a ON s.student_id = a.student_id
    AND a.subject_id = ? AND a.date = ?
ORDER BY s.roll_number
```

### 3. Mark Attendance (Transaction)

```sql
-- Location: api/mark_attendance.php
BEGIN TRANSACTION;
DELETE FROM attendance WHERE subject_id = ? AND date = ?;
INSERT INTO attendance (student_id, subject_id, teacher_id, date, status)
VALUES (?, ?, ?, ?, ?);
COMMIT;
```

## Database Schema

### Tables

1. **students** - Student information
2. **teachers** - Teacher information
3. **subjects** - Subject details
4. **subject_teacher_mapping** - Maps teachers to subjects
5. **attendance** - Attendance records

### Relationships

- `attendance.student_id` → `students.student_id` (Foreign Key)
- `attendance.subject_id` → `subjects.subject_id` (Foreign Key)
- `attendance.teacher_id` → `teachers.teacher_id` (Foreign Key)
- `subject_teacher_mapping.subject_id` → `subjects.subject_id` (Foreign Key)
- `subject_teacher_mapping.teacher_id` → `teachers.teacher_id` (Foreign Key)

## Troubleshooting

### Database Connection Error

- Verify MySQL is running
- Check database credentials in `config.php`
- Ensure database `nsut_attendance` exists

### Login Not Working

- Check if database tables are created
- Verify credentials match those in database
- Check PHP session is enabled

### Attendance Not Saving

- Check browser console for JavaScript errors
- Verify API endpoints are accessible
- Check MySQL error logs

## Technologies Used

- **Frontend**: HTML5, CSS3, Bootstrap 5.3, JavaScript (ES6)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB
- **Icons**: Font Awesome 6.4
- **Server**: Apache/Nginx or PHP Built-in Server

## Security Notes

⚠️ **This is a demo project for educational purposes**

For production use, implement:
- Password hashing (bcrypt/argon2)
- CSRF protection
- Input validation and sanitization
- Session security measures
- HTTPS encryption
- Role-based access control (RBAC)

## License

This project is created for educational purposes as a SQL demonstration project.

## Author

Created for NSUT IT Branch SQL Project Demonstration

---

**Note**: All data in this application is mock/dummy data for demonstration purposes only.
