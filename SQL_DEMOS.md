# SQL Demonstrations - Reference Guide

This document provides a comprehensive reference to all SQL operations demonstrated in this project.

## Table of Contents
1. [Database Schema (database.sql)](#database-schema)
2. [Authentication (login.php)](#authentication)
3. [Teacher Operations](#teacher-operations)
4. [Student Operations](#student-operations)
5. [Advanced SQL Concepts](#advanced-sql-concepts)

---

## Database Schema

### Location: `database.sql`

#### DEMO 1: CREATE TABLE with Constraints
```sql
-- Creating Students table with primary key and unique constraints
CREATE TABLE students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    roll_number VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    branch VARCHAR(50) DEFAULT 'IT',
    semester INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
**Concepts**: Primary Key, Auto Increment, Unique Constraint, NOT NULL, DEFAULT values

---

#### DEMO 2: Foreign Key Relationships
```sql
CREATE TABLE teachers (
    teacher_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    department VARCHAR(50) DEFAULT 'IT',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
**Concepts**: Table creation with constraints

---

#### DEMO 3: Composite Foreign Keys
```sql
CREATE TABLE subject_teacher_mapping (
    mapping_id INT PRIMARY KEY AUTO_INCREMENT,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    academic_year VARCHAR(20) DEFAULT '2024-25',
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id) ON DELETE CASCADE,
    UNIQUE KEY unique_subject_teacher (subject_id, teacher_id, academic_year)
);
```
**Concepts**: Foreign Keys, ON DELETE CASCADE, Composite Unique Keys

---

#### DEMO 4: ENUM Data Type
```sql
CREATE TABLE attendance (
    attendance_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Present', 'Absent') NOT NULL,
    marked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (student_id, subject_id, date)
);
```
**Concepts**: ENUM type, Multiple Foreign Keys, Composite Unique Constraint

---

#### DEMO 5: Bulk INSERT
```sql
INSERT INTO students (roll_number, name, email, password, semester) VALUES
('2021IT001', 'Aarav Sharma', 'aarav.sharma@nsut.ac.in', 'pass001', 5),
('2021IT002', 'Vivaan Gupta', 'vivaan.gupta@nsut.ac.in', 'pass002', 5),
-- ... 18 more rows
('2021IT020', 'Isha Bhatia', 'isha.bhatia@nsut.ac.in', 'pass020', 5);
```
**Concepts**: Multiple row INSERT in single query

---

#### DEMO 6: INSERT with Subqueries
```sql
INSERT INTO subject_teacher_mapping (subject_id, teacher_id) VALUES
((SELECT subject_id FROM subjects WHERE subject_code = 'IT301'),
 (SELECT teacher_id FROM teachers WHERE email = 'rajesh.kumar@nsut.ac.in'));
```
**Concepts**: Nested SELECT in INSERT, Subqueries

---

#### DEMO 7: CREATE VIEW
```sql
CREATE VIEW student_attendance_summary AS
SELECT
    s.student_id,
    s.roll_number,
    s.name AS student_name,
    subj.subject_id,
    subj.subject_code,
    subj.subject_name,
    COUNT(a.attendance_id) AS total_classes,
    SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS classes_attended,
    ROUND((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.attendance_id)) * 100, 2) AS attendance_percentage
FROM students s
CROSS JOIN subjects subj
LEFT JOIN attendance a ON s.student_id = a.student_id AND subj.subject_id = a.subject_id
GROUP BY s.student_id, subj.subject_id;
```
**Concepts**: VIEW creation, CROSS JOIN, LEFT JOIN, GROUP BY, Aggregate functions

---

## Authentication

### Location: `api/login.php`

#### DEMO 8: Prepared Statements (Preventing SQL Injection)
```php
// SQL DEMO: SELECT query with prepared statements
$sql = "SELECT student_id, roll_number, name, email
        FROM students
        WHERE email = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();
```
**Concepts**:
- Prepared Statements
- Parameter Binding
- SQL Injection Prevention
- WHERE clause with multiple conditions

---

## Teacher Operations

### Location: `api/get_teacher_subjects.php`

#### DEMO 9: INNER JOIN
```sql
-- Fetching subjects assigned to a teacher using INNER JOIN
SELECT s.subject_id, s.subject_code, s.subject_name, s.semester, s.credits
FROM subjects s
INNER JOIN subject_teacher_mapping stm ON s.subject_id = stm.subject_id
WHERE stm.teacher_id = ?
ORDER BY s.subject_code
```
**Concepts**:
- INNER JOIN
- Table aliasing (s, stm)
- WHERE with JOIN
- ORDER BY

---

### Location: `api/get_students.php`

#### DEMO 10: LEFT JOIN with Multiple Conditions
```sql
-- Fetching all students with their attendance status for a specific date
-- Using LEFT JOIN to include students even if attendance not marked
SELECT
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
ORDER BY s.roll_number
```
**Concepts**:
- LEFT JOIN (includes all left table rows)
- Multiple JOIN conditions
- NULL handling (when no attendance found)

---

### Location: `api/mark_attendance.php`

#### DEMO 11: Transaction with DELETE and INSERT
```php
// Start transaction for data consistency
$conn->begin_transaction();

try {
    // SQL DEMO: DELETE query
    $delete_sql = "DELETE FROM attendance WHERE subject_id = ? AND date = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("is", $subject_id, $date);
    $delete_stmt->execute();

    // SQL DEMO: INSERT query in a loop (Bulk insert)
    $insert_sql = "INSERT INTO attendance (student_id, subject_id, teacher_id, date, status)
                   VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);

    foreach ($attendance as $record) {
        $insert_stmt->bind_param("iiiss", $student_id, $subject_id, $teacher_id, $date, $status);
        $insert_stmt->execute();
    }

    // Commit transaction
    $conn->commit();
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
}
```
**Concepts**:
- Database Transactions
- ACID properties
- DELETE with WHERE
- Bulk INSERT in loop
- Error handling with rollback
- COMMIT and ROLLBACK

---

## Student Operations

### Location: `api/get_student_attendance.php`

#### DEMO 12: GROUP BY with Aggregate Functions
```sql
-- Complex query with GROUP BY and aggregate functions
-- Calculating attendance percentage for each subject
SELECT
    s.subject_id,
    s.subject_code,
    s.subject_name,
    s.credits,
    COUNT(a.attendance_id) AS total_classes,
    SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS classes_attended,
    SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS classes_missed,
    CASE
        WHEN COUNT(a.attendance_id) > 0 THEN
            ROUND((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.attendance_id)) * 100, 2)
        ELSE 0
    END AS attendance_percentage
FROM subjects s
LEFT JOIN attendance a ON s.subject_id = a.subject_id AND a.student_id = ?
GROUP BY s.subject_id, s.subject_code, s.subject_name, s.credits
ORDER BY s.subject_code
```
**Concepts**:
- GROUP BY with multiple columns
- COUNT() aggregate function
- SUM() with CASE statements
- ROUND() for decimal precision
- Conditional CASE WHEN
- Calculated columns
- Division by zero handling

---

#### DEMO 13: Overall Attendance Calculation
```sql
-- Calculating overall attendance across all subjects
SELECT
    COUNT(attendance_id) AS total_classes,
    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS total_present,
    CASE
        WHEN COUNT(attendance_id) > 0 THEN
            ROUND((SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) / COUNT(attendance_id)) * 100, 2)
        ELSE 0
    END AS overall_percentage
FROM attendance
WHERE student_id = ?
```
**Concepts**:
- Aggregate functions on single table
- CASE within aggregate functions
- Percentage calculations

---

## Advanced SQL Concepts

### 1. Database Design Principles

#### Normalization
- **1NF**: Each column contains atomic values (no arrays)
- **2NF**: No partial dependencies (all non-key attributes depend on full primary key)
- **3NF**: No transitive dependencies (no non-key attribute depends on another non-key attribute)

#### Relationships Demonstrated
- **One-to-Many**: Teacher → Subjects (via mapping table)
- **Many-to-Many**: Students ↔ Subjects (via attendance table)
- **One-to-Many**: Subject → Attendance records

---

### 2. Query Optimization Techniques Used

#### Indexing
```sql
-- Primary keys automatically create indexes
PRIMARY KEY (student_id)

-- Unique constraints create indexes
UNIQUE KEY (email)

-- Composite unique key creates compound index
UNIQUE KEY unique_attendance (student_id, subject_id, date)
```

#### Prepared Statements Benefits
- Query plan caching
- Prevention of SQL injection
- Type safety
- Performance improvement for repeated queries

---

### 3. Data Integrity Constraints

#### Referential Integrity
```sql
FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
```
- Ensures attendance records only exist for valid students
- CASCADE deletes attendance when student is deleted

#### Domain Constraints
```sql
status ENUM('Present', 'Absent') NOT NULL
```
- Limits values to predefined set
- Prevents invalid status values

---

### 4. SQL Functions Used

| Function | Purpose | Example |
|----------|---------|---------|
| COUNT() | Count rows | `COUNT(attendance_id)` |
| SUM() | Sum values | `SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END)` |
| ROUND() | Round decimals | `ROUND(percentage, 2)` |
| NOW() | Current timestamp | `TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |
| CASE WHEN | Conditional logic | `CASE WHEN count > 0 THEN ... END` |

---

### 5. JOIN Types Explained

#### INNER JOIN
- Returns only matching rows from both tables
- Used in: `get_teacher_subjects.php`

#### LEFT JOIN
- Returns all rows from left table, matched rows from right
- NULL for non-matching right table rows
- Used in: `get_students.php`, `get_student_attendance.php`

#### CROSS JOIN
- Cartesian product of two tables
- Used in: VIEW definition

---

## SQL Best Practices Demonstrated

1. **Always use prepared statements** - Prevents SQL injection
2. **Use transactions for multi-step operations** - Ensures data consistency
3. **Index frequently queried columns** - Improves performance
4. **Use appropriate data types** - ENUM for status, DATE for dates
5. **Normalize database structure** - Reduces redundancy
6. **Use meaningful table/column names** - Improves readability
7. **Add constraints** - Ensures data integrity
8. **Use aliases** - Makes queries more readable
9. **Handle NULL values** - Use LEFT JOIN and CASE appropriately
10. **Comment complex queries** - Helps future maintenance

---

## Testing SQL Queries

You can test these queries directly in phpMyAdmin or MySQL CLI:

```sql
-- Test: Get student's attendance for all subjects
SELECT s.subject_code,
       COUNT(a.attendance_id) as total,
       SUM(CASE WHEN a.status='Present' THEN 1 ELSE 0 END) as present
FROM subjects s
LEFT JOIN attendance a ON s.subject_id = a.subject_id AND a.student_id = 1
GROUP BY s.subject_id;

-- Test: Get teacher's subjects
SELECT s.subject_name, t.name as teacher_name
FROM subjects s
INNER JOIN subject_teacher_mapping stm ON s.subject_id = stm.subject_id
INNER JOIN teachers t ON stm.teacher_id = t.teacher_id
WHERE t.teacher_id = 1;

-- Test: Overall attendance percentage
SELECT
    COUNT(*) as total,
    SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) as present,
    ROUND((SUM(CASE WHEN status='Present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as percentage
FROM attendance
WHERE student_id = 1;
```

---

## Learning Resources

To learn more about the SQL concepts used in this project:

- **JOINs**: Understanding different join types and when to use them
- **Aggregate Functions**: GROUP BY, COUNT, SUM, AVG, etc.
- **Subqueries**: Using SELECT within SELECT
- **Transactions**: ACID properties and data consistency
- **Prepared Statements**: Security and performance benefits
- **Database Design**: Normalization and relationships

---

**Note**: All SQL queries in this project are commented with "SQL DEMO" markers in the source code for easy identification.
