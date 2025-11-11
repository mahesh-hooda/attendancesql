# NSUT IT Branch - Teacher Attendance Portal (SQLite)

A lightweight, Bootstrap-based attendance management system for demonstrating SQL operations using SQLite database.

## Features

- **Teacher Portal**: Mark attendance for students by subject and date
- **Student Portal**: View attendance records and percentage for each subject
- **SQL Demonstrations**: Extensive use of SQL queries with detailed comments
- **Bootstrap UI**: Responsive, modern interface
- **Mock Data**: Pre-populated with 20 students, 5 teachers, and 5 subjects
- **SQLite Database**: Zero configuration, file-based database
- **PDO**: Database abstraction layer for better portability

## Why SQLite?

- **Zero Configuration**: No database server setup required
- **Portable**: Single file database, easy to backup and move
- **Lightweight**: Perfect for demos and small applications
- **ACID Compliant**: Full transaction support
- **Standard SQL**: Most SQL features work as expected

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
9. **Prepared Statements** - preventing SQL injection with PDO
10. **Transactions** - ensuring data consistency
11. **CREATE VIEW** - for attendance summaries
12. **Complex JOINs** - multiple table relationships
13. **CHECK Constraints** - SQLite's alternative to ENUM

## Project Structure

```
attendancesql/
├── index.php                    # Login page (both student & teacher)
├── teacher_dashboard.php        # Teacher interface for marking attendance
├── student_dashboard.php        # Student interface for viewing attendance
├── config.php                   # SQLite/PDO configuration
├── logout.php                   # Session cleanup
├── database.sql                 # SQLite database schema with mock data
├── init_database.php            # Database initialization script
├── .htaccess                    # Apache configuration
├── api/
│   ├── login.php               # Authentication API (PDO SELECT demo)
│   ├── get_teacher_subjects.php # Fetch teacher's subjects (JOIN demo)
│   ├── get_students.php        # Fetch students with attendance (LEFT JOIN demo)
│   ├── mark_attendance.php     # Save attendance records (INSERT/DELETE demo)
│   └── get_student_attendance.php # Attendance statistics (GROUP BY demo)
├── README.md                    # This file
└── INSTALLATION_GUIDE_SQLITE.md # Quick setup guide
```

## Quick Start (3 Steps)

```bash
cd attendancesql
php init_database.php      # Creates and populates database
php -S localhost:8000      # Starts web server
# Open http://localhost:8000
```

## Setup Instructions

### Prerequisites

- PHP 7.4 or higher **with PDO SQLite extension**
- Apache/Nginx web server (or PHP built-in server)
- SQLite 3 (usually pre-installed on most systems)

**Check PHP SQLite Support:**
```bash
php -m | grep -i pdo_sqlite
```

If not installed:
- **Ubuntu/Debian**: `sudo apt-get install php-sqlite3`
- **macOS**: Included by default with PHP
- **Windows (XAMPP)**: Included by default

### Installation Steps

#### 1. Clone/Download the Project

```bash
git clone <repository-url>
cd attendancesql
```

#### 2. Initialize Database

**Option A: Using PHP Script (Recommended)**

```bash
php init_database.php
```

This will:
- Create `nsut_attendance.db` file
- Create all tables
- Insert mock data (20 students, 5 teachers, 5 subjects)
- Show statistics

**Option B: Using SQLite CLI**

```bash
sqlite3 nsut_attendance.db < database.sql
```

#### 3. Set Permissions (Linux/Mac only)

```bash
chmod 666 nsut_attendance.db
chmod 777 .
```

#### 4. Start Web Server

**For PHP Built-in Server:**
```bash
php -S localhost:8000
```

**For XAMPP/WAMP:**
- Copy project folder to `htdocs/` or `www/`
- Start Apache (MySQL NOT required!)
- Access: `http://localhost/attendancesql/`

#### 5. Access the Application

Open your browser and navigate to `http://localhost:8000`

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

## Verify Installation

```bash
# Check if database exists
ls -lh nsut_attendance.db

# Count students
sqlite3 nsut_attendance.db "SELECT COUNT(*) FROM students;"
# Should return: 20

# View first student
sqlite3 nsut_attendance.db "SELECT roll_number, name FROM students LIMIT 1;"
# Should return: 2021IT001|Aarav Sharma
```

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
    ROUND((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) * 100.0 / COUNT(a.attendance_id)), 2) AS attendance_percentage
FROM subjects s
LEFT JOIN attendance a ON s.subject_id = a.subject_id AND a.student_id = :student_id
GROUP BY s.subject_id
```

**Note**: SQLite requires `100.0` (not `100`) for float division.

### 2. Fetch Students with Attendance Status (LEFT JOIN)

```sql
-- Location: api/get_students.php
SELECT s.student_id, s.roll_number, s.name, a.status
FROM students s
LEFT JOIN attendance a ON s.student_id = a.student_id
    AND a.subject_id = :subject_id AND a.date = :date
ORDER BY s.roll_number
```

**Note**: Using PDO named parameters (`:param`) for security.

### 3. Mark Attendance (Transaction)

```sql
-- Location: api/mark_attendance.php
BEGIN TRANSACTION;
DELETE FROM attendance WHERE subject_id = :subject_id AND date = :date;
INSERT INTO attendance (student_id, subject_id, teacher_id, date, status)
VALUES (:student_id, :subject_id, :teacher_id, :date, :status);
COMMIT;
```

**Note**: PDO uses `beginTransaction()` and `commit()` methods.

## Database Schema

### Tables

1. **students** - Student information
2. **teachers** - Teacher information
3. **subjects** - Subject details
4. **subject_teacher_mapping** - Maps teachers to subjects
5. **attendance** - Attendance records

### Relationships

- `attendance.student_id` → `students.student_id` (Foreign Key, CASCADE)
- `attendance.subject_id` → `subjects.subject_id` (Foreign Key, CASCADE)
- `attendance.teacher_id` → `teachers.teacher_id` (Foreign Key, CASCADE)
- `subject_teacher_mapping.subject_id` → `subjects.subject_id` (Foreign Key, CASCADE)
- `subject_teacher_mapping.teacher_id` → `teachers.teacher_id` (Foreign Key, CASCADE)

**Note**: Foreign keys are enabled with `PRAGMA foreign_keys = ON;` in config.php

## Viewing Database Contents

### Using SQLite CLI

```bash
sqlite3 nsut_attendance.db

# List tables
.tables

# Show table schema
.schema students

# Query data
SELECT * FROM students LIMIT 5;

# Exit
.quit
```

### Using DB Browser for SQLite

Download: https://sqlitebrowser.org/
- Open `nsut_attendance.db`
- Browse tables, run queries visually

## Troubleshooting

### "could not find driver" Error

**Solution:**
```bash
# Check if PDO SQLite is installed
php -m | grep pdo_sqlite

# Install if missing (Ubuntu/Debian)
sudo apt-get install php-sqlite3
sudo systemctl restart apache2
```

### "attempt to write a readonly database" Error

**Solution:**
```bash
# Make database and directory writable
chmod 666 nsut_attendance.db
chmod 777 .
```

### Database Locked Error

**Solution:**
- Close all other connections to the database
- Remove `.db-journal` files if database is not in use
- Ensure proper transaction handling (commit/rollback)

### Login Not Working

- Run `php init_database.php` to recreate database
- Check if database file exists: `ls -lh nsut_attendance.db`
- Verify credentials match those in database
- Check PHP session is enabled

### Attendance Not Saving

- Check browser console for JavaScript errors
- Verify API endpoints are accessible
- Check database file permissions (should be writable)

## Technologies Used

- **Frontend**: HTML5, CSS3, Bootstrap 5.3, JavaScript (ES6)
- **Backend**: PHP 7.4+ with PDO
- **Database**: SQLite 3
- **Icons**: Font Awesome 6.4
- **Server**: Apache/Nginx or PHP Built-in Server

## SQLite-Specific Features

### Data Type Conversions

```
MySQL                    →  SQLite
─────────────────────────────────────
INT AUTO_INCREMENT       →  INTEGER AUTOINCREMENT
VARCHAR(100)             →  TEXT
TIMESTAMP                →  DATETIME
ENUM('A', 'B')          →  TEXT CHECK(col IN ('A', 'B'))
```

### Float Division

SQLite requires explicit float in division:
```sql
-- MySQL: (sum / count) * 100
-- SQLite: (sum * 100.0 / count)  -- Note: 100.0 not 100
```

### Foreign Keys

Foreign keys must be explicitly enabled:
```php
$conn->exec('PRAGMA foreign_keys = ON;');
```

### PDO Named Parameters

All queries use PDO named parameters:
```php
$stmt = $conn->prepare("SELECT * FROM students WHERE email = :email");
$stmt->execute(['email' => $email]);
```

## Backup and Restore

### Backup
```bash
# Simple copy
cp nsut_attendance.db backup.db

# SQL dump
sqlite3 nsut_attendance.db .dump > backup.sql
```

### Restore
```bash
# From copy
cp backup.db nsut_attendance.db

# From SQL dump
sqlite3 nsut_attendance.db < backup.sql
```

## Security Notes

⚠️ **This is a demo project for educational purposes**

For production use, implement:
- Password hashing (bcrypt/argon2) - currently using plain text
- CSRF protection
- Input validation and sanitization
- Session security measures (secure cookies, regeneration)
- HTTPS encryption
- Rate limiting for login attempts
- Prepared statements (✓ already implemented with PDO)

## Advantages of SQLite for This Project

✓ **Zero Setup** - No MySQL server configuration needed
✓ **Portable** - Single file, easy to share and backup
✓ **Fast** - Perfect for small to medium datasets
✓ **ACID Compliant** - Full transaction support
✓ **Cross-platform** - Works on Windows, Mac, Linux
✓ **No Authentication** - Simpler for demos and development
✓ **Easy Backup** - Just copy the .db file

## Additional Documentation

- **INSTALLATION_GUIDE_SQLITE.md** - Detailed platform-specific setup instructions
- **SQL_DEMOS.md** - Comprehensive SQL reference with all 13 demonstrations
- **database.sql** - Well-commented schema with SQL demos

## License

This project is created for educational purposes as a SQL demonstration project.

## Author

Created for NSUT IT Branch SQL Project Demonstration

---

**Note**: All data in this application is mock/dummy data for demonstration purposes only.
