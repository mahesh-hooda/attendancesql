# Quick Installation Guide - SQLite Version

## For PHP Built-in Server (Any OS - Quickest)

### Step 1: Check PHP SQLite Support
```bash
php -m | grep -i pdo_sqlite
```

If you see "pdo_sqlite", you're good to go! If not, see installation instructions below.

### Step 2: Navigate to Project
```bash
cd attendancesql
```

### Step 3: Initialize Database
```bash
php init_database.php
```

Expected output:
```
Starting database initialization...
✓ Created table: students
✓ Created table: teachers
✓ Inserted 20 rows into students
...
✓ Database is ready to use!
```

### Step 4: Start PHP Server
```bash
php -S localhost:8000
```

### Step 5: Access Application
Open browser: `http://localhost:8000`

---

## For XAMPP Users (Windows/Mac/Linux)

### Step 1: Install XAMPP
Download from: https://www.apachefriends.org/

XAMPP includes SQLite support by default!

### Step 2: Place Files
Copy the `attendancesql` folder to:
- **Windows**: `C:\xampp\htdocs\`
- **Mac**: `/Applications/XAMPP/htdocs/`
- **Linux**: `/opt/lampp/htdocs/`

### Step 3: Start Services
- Open XAMPP Control Panel
- Start **Apache** service
- MySQL is NOT needed for SQLite!

### Step 4: Initialize Database
**Option A: Via Browser**
- Navigate to: `http://localhost/attendancesql/init_database.php`
- You should see success messages

**Option B: Via Terminal**
```bash
cd C:\xampp\htdocs\attendancesql  # Windows
# or
cd /Applications/XAMPP/htdocs/attendancesql  # Mac

php init_database.php
```

### Step 5: Set Permissions (Mac/Linux only)
```bash
chmod 666 nsut_attendance.db
chmod 777 .
```

### Step 6: Access Application
Open browser: `http://localhost/attendancesql/`

---

## For Ubuntu/Debian

### Step 1: Install PHP with SQLite
```bash
sudo apt update
sudo apt install php php-sqlite3 php-cli
```

### Step 2: Clone Project
```bash
git clone <repository-url>
cd attendancesql
```

### Step 3: Initialize Database
```bash
php init_database.php
```

### Step 4: Set Permissions
```bash
chmod 666 nsut_attendance.db
chmod 777 .
```

### Step 5: Start Server
```bash
php -S localhost:8000
```

### Step 6: Access
Open: `http://localhost:8000`

---

## For macOS

### Step 1: Check/Install PHP
macOS comes with PHP, but check version:
```bash
php --version
```

If PHP < 7.4 or not installed:
```bash
brew install php
```

### Step 2: Verify SQLite Support
```bash
php -m | grep pdo_sqlite
```

Should show: `pdo_sqlite` (included by default on macOS)

### Step 3: Navigate and Initialize
```bash
cd attendancesql
php init_database.php
```

### Step 4: Set Permissions
```bash
chmod 666 nsut_attendance.db
chmod 777 .
```

### Step 5: Start Server
```bash
php -S localhost:8000
```

### Step 6: Access
Open: `http://localhost:8000`

---

## Using SQLite CLI (Alternative)

If you prefer using SQLite command-line:

### Step 1: Install SQLite3
**Ubuntu/Debian:**
```bash
sudo apt install sqlite3
```

**macOS:**
```bash
brew install sqlite3
```

**Windows:**
Download from: https://www.sqlite.org/download.html

### Step 2: Create Database
```bash
cd attendancesql
sqlite3 nsut_attendance.db < database.sql
```

### Step 3: Verify Creation
```bash
sqlite3 nsut_attendance.db "SELECT COUNT(*) FROM students;"
```

Should show: `20`

### Step 4: Start Web Server
```bash
php -S localhost:8000
```

---

## Verification Checklist

After installation, verify:

- [ ] Database file `nsut_attendance.db` exists
- [ ] Can access login page
- [ ] Can login as teacher (rajesh.kumar@nsut.ac.in / teacher001)
- [ ] Teacher can see 5 subjects
- [ ] Teacher can mark attendance
- [ ] Can login as student (aarav.sharma@nsut.ac.in / pass001)
- [ ] Student can view attendance dashboard
- [ ] Attendance percentages are calculated correctly

---

## Quick Test Commands

```bash
# Check if database exists
ls -lh nsut_attendance.db

# Count students
sqlite3 nsut_attendance.db "SELECT COUNT(*) FROM students;"
# Should return: 20

# Count teachers
sqlite3 nsut_attendance.db "SELECT COUNT(*) FROM teachers;"
# Should return: 5

# Check first student
sqlite3 nsut_attendance.db "SELECT roll_number, name FROM students LIMIT 1;"
# Should return: 2021IT001|Aarav Sharma

# View all tables
sqlite3 nsut_attendance.db ".tables"
# Should show: attendance students subject_teacher_mapping subjects teachers
```

---

## Common Issues & Solutions

### Issue: "could not find driver"
**Solution:**
```bash
# Install PDO SQLite
sudo apt-get install php-sqlite3  # Ubuntu/Debian
brew reinstall php  # macOS

# Restart web server
sudo systemctl restart apache2  # Linux
```

### Issue: "attempt to write a readonly database"
**Solution:**
```bash
chmod 666 nsut_attendance.db
chmod 777 .
```

### Issue: "database is locked"
**Solution:**
```bash
# Close any open connections, then
rm nsut_attendance.db-journal
```

### Issue: Database file not created
**Solution:**
```bash
# Create manually
touch nsut_attendance.db
chmod 666 nsut_attendance.db
php init_database.php
```

---

## Demo Credentials

### Quick Access

**Teacher:**
```
Email: rajesh.kumar@nsut.ac.in
Password: teacher001
```

**Student:**
```
Email: aarav.sharma@nsut.ac.in
Password: pass001
```

### All Teachers
- teacher001 to teacher005
- Emails: {firstname}.{lastname}@nsut.ac.in

### All Students
- pass001 to pass020
- Roll: 2021IT001 to 2021IT020

---

## Next Steps

1. Login as a teacher and mark some attendance
2. Login as a student and view the attendance statistics
3. Explore the database using SQLite CLI or DB Browser
4. Check `SQL_DEMOS.md` for detailed SQL query explanations
5. Review the code to understand PDO implementation

---

## Advantages of This Setup

✓ **No MySQL Required** - Simpler installation
✓ **Single File Database** - Easy to backup (just copy .db file)
✓ **Portable** - Move entire folder anywhere
✓ **Zero Configuration** - Works immediately after init
✓ **Perfect for Demos** - Quick setup for presentations
✓ **Cross-platform** - Same commands work everywhere

---

## Need Help?

- Check main documentation: `README_SQLITE.md`
- View SQL examples: `SQL_DEMOS.md`
- Browse code comments (marked with "SQL DEMO")
- Use SQLite CLI to inspect database directly

---

**Happy Learning! 🎓**
