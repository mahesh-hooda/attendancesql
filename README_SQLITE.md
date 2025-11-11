# NSUT IT Branch - Teacher Attendance Portal (SQLite Version)

A lightweight, Bootstrap-based attendance management system for demonstrating SQL operations using SQLite database.

## Features

- **Teacher Portal**: Mark attendance for students by subject and date
- **Student Portal**: View attendance records and percentage for each subject
- **SQL Demonstrations**: Extensive use of SQL queries with detailed comments
- **Bootstrap UI**: Responsive, modern interface
- **Mock Data**: Pre-populated with 20 students, 5 teachers, and 5 subjects
- **SQLite Database**: No server required, file-based database
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
└── README_SQLITE.md             # This file
```

## Setup Instructions

### Prerequisites

- PHP 7.4 or higher **with PDO SQLite extension**
- Apache/Nginx web server (or PHP built-in server)
- SQLite 3

### Check PHP SQLite Support

```bash
php -m | grep -i pdo_sqlite
```

If not installed, install it:

**Ubuntu/Debian:**
```bash
sudo apt-get install php-sqlite3
```

**macOS (Homebrew):**
```bash
brew install php
# SQLite support is included by default
```

**Windows (XAMPP):**
- SQLite support is included by default in XAMPP

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
- Create the `nsut_attendance.db` file
- Create all tables
- Insert mock data (20 students, 5 teachers, 5 subjects)
- Show statistics

**Option B: Using SQLite CLI**

```bash
sqlite3 nsut_attendance.db < database.sql
```

**Option C: Manual Import**

```bash
# Create database file
sqlite3 nsut_attendance.db

# Inside SQLite prompt
.read database.sql
.quit
```

#### 3. Verify Database

```bash
# Check database was created
ls -lh nsut_attendance.db

# View tables
sqlite3 nsut_attendance.db ".tables"

# Check student count
sqlite3 nsut_attendance.db "SELECT COUNT(*) FROM students;"
```

#### 4. Set Permissions

```bash
# Make database writable by web server
chmod 666 nsut_attendance.db
chmod 777 .  # Directory must be writable for SQLite
```

#### 5. Start Web Server

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

## SQLite-Specific Features

### 1. Data Types

SQLite uses dynamic typing with type affinity:

```sql
-- MySQL                       -- SQLite
INT AUTO_INCREMENT      →      INTEGER AUTOINCREMENT
VARCHAR(100)            →      TEXT
TIMESTAMP               →      DATETIME
ENUM('A', 'B')         →      TEXT CHECK(column IN ('A', 'B'))
```

### 2. Foreign Keys

Foreign keys must be explicitly enabled in SQLite:

```php
// In config.php
$conn->exec('PRAGMA foreign_keys = ON;');
```

### 3. Float Division

SQLite requires explicit float in division:

```sql
-- MySQL
(sum / count) * 100

-- SQLite
(sum * 100.0 / count)  -- Note: 100.0 not 100
```

### 4. PDO Named Parameters

All queries use PDO named parameters for security:

```php
$stmt = $conn->prepare("SELECT * FROM students WHERE email = :email");
$stmt->execute(['email' => $email]);
```

## Database Schema

### Tables

1. **students** - Student information (20 records)
2. **teachers** - Teacher information (5 records)
3. **subjects** - Subject details (5 records)
4. **subject_teacher_mapping** - Maps teachers to subjects
5. **attendance** - Attendance records

### Relationships

- `attendance.student_id` → `students.student_id` (Foreign Key, CASCADE)
- `attendance.subject_id` → `subjects.subject_id` (Foreign Key, CASCADE)
- `attendance.teacher_id` → `teachers.teacher_id` (Foreign Key, CASCADE)
- `subject_teacher_mapping.subject_id` → `subjects.subject_id` (Foreign Key, CASCADE)
- `subject_teacher_mapping.teacher_id` → `teachers.teacher_id` (Foreign Key, CASCADE)

## Viewing Database Contents

### Using SQLite CLI

```bash
# Open database
sqlite3 nsut_attendance.db

# List tables
.tables

# Show table schema
.schema students

# Query data
SELECT * FROM students LIMIT 5;

# Show attendance summary
SELECT
    s.name,
    COUNT(*) as classes,
    SUM(CASE WHEN a.status='Present' THEN 1 ELSE 0 END) as present
FROM students s
JOIN attendance a ON s.student_id = a.student_id
GROUP BY s.student_id;

# Exit
.quit
```

### Using DB Browser for SQLite

Download: https://sqlitebrowser.org/

- Open `nsut_attendance.db`
- Browse tables, run queries, view data visually

## Troubleshooting

### Database Connection Error

**Error:** `could not find driver`

**Solution:**
```bash
# Check if PDO SQLite is installed
php -m | grep pdo_sqlite

# Install if missing (Ubuntu/Debian)
sudo apt-get install php-sqlite3
sudo systemctl restart apache2
```

### Permission Denied

**Error:** `attempt to write a readonly database`

**Solution:**
```bash
# Make database and directory writable
chmod 666 nsut_attendance.db
chmod 777 .
```

### Database Locked

**Error:** `database is locked`

**Solution:**
- Close all other connections to the database
- Check for `.db-journal` files and remove them if database is not in use
- Ensure proper transaction handling (commit/rollback)

### Foreign Key Constraint Failed

**Error:** `FOREIGN KEY constraint failed`

**Solution:**
- Ensure foreign keys are enabled: `PRAGMA foreign_keys = ON;`
- Check that referenced records exist
- Verify cascade rules are properly set

## Performance Tips

1. **Enable WAL Mode** (for concurrent reads):
```sql
PRAGMA journal_mode=WAL;
```

2. **Add Indexes** for frequently queried columns:
```sql
CREATE INDEX idx_attendance_student ON attendance(student_id);
CREATE INDEX idx_attendance_subject ON attendance(subject_id);
```

3. **Use Transactions** for bulk operations (already implemented)

## Backup and Restore

### Backup

```bash
# Simple copy
cp nsut_attendance.db nsut_attendance_backup.db

# SQL dump
sqlite3 nsut_attendance.db .dump > backup.sql

# With compression
tar -czf backup.tar.gz nsut_attendance.db
```

### Restore

```bash
# From copy
cp nsut_attendance_backup.db nsut_attendance.db

# From SQL dump
sqlite3 nsut_attendance.db < backup.sql
```

## Technologies Used

- **Frontend**: HTML5, CSS3, Bootstrap 5.3, JavaScript (ES6)
- **Backend**: PHP 7.4+ with PDO
- **Database**: SQLite 3
- **Icons**: Font Awesome 6.4
- **Server**: Apache/Nginx or PHP Built-in Server

## Security Notes

⚠️ **This is a demo project for educational purposes**

For production use, implement:
- Password hashing (bcrypt/argon2) - currently using plain text
- CSRF protection
- Input validation and sanitization
- Session security measures (secure cookies, regeneration)
- HTTPS encryption
- Rate limiting for login attempts
- Prepared statements (✓ already implemented)

## Advantages of SQLite for This Project

✓ **Zero Setup** - No MySQL server configuration needed
✓ **Portable** - Single file, easy to share and backup
✓ **Fast** - Perfect for small to medium datasets
✓ **ACID Compliant** - Full transaction support
✓ **Cross-platform** - Works on Windows, Mac, Linux
✓ **No Authentication** - Simpler for demos and development
✓ **Easy Backup** - Just copy the .db file

## Limitations to Consider

- Single write operation at a time (readers can still access)
- Not suitable for high-concurrency write scenarios
- Maximum database size: 281 TB (more than enough for this use case)
- No built-in user management (handled by application layer)

## Converting Back to MySQL

If you need to switch back to MySQL:

1. Use the original `database.sql` with MySQL syntax
2. Update `config.php` to use MySQLi instead of PDO
3. Replace named parameters (`:param`) with question marks (`?`)
4. Replace `beginTransaction()` with `begin_transaction()`
5. Replace `INTEGER` with `INT`, `TEXT` with `VARCHAR()`, etc.

## License

This project is created for educational purposes as a SQL demonstration project.

## Author

Created for NSUT IT Branch SQL Project Demonstration

---

**Note**: All data in this application is mock/dummy data for demonstration purposes only.
