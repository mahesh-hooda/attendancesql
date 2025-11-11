# Quick Installation Guide

## For XAMPP Users (Windows/Mac/Linux)

### Step 1: Start XAMPP
- Open XAMPP Control Panel
- Start **Apache** and **MySQL** services

### Step 2: Place Files
- Copy the `attendancesql` folder to:
  - **Windows**: `C:\xampp\htdocs\`
  - **Mac/Linux**: `/Applications/XAMPP/htdocs/` or `/opt/lampp/htdocs/`

### Step 3: Create Database
- Open browser: `http://localhost/phpmyadmin`
- Click "New" to create database
- Database name: `nsut_attendance`
- Collation: `utf8mb4_general_ci`
- Click "Create"

### Step 4: Import SQL
- Select `nsut_attendance` database
- Click "Import" tab
- Click "Choose File"
- Select `database.sql` from the project folder
- Click "Go" at the bottom
- Wait for success message

### Step 5: Access Application
- Open browser: `http://localhost/attendancesql/`
- Login with demo credentials (see README.md)

## For WAMP Users (Windows)

### Step 1: Start WAMP
- Start WAMP server (icon should be green)

### Step 2: Place Files
- Copy the `attendancesql` folder to: `C:\wamp64\www\`

### Step 3-5: Same as XAMPP
- Follow steps 3-5 from XAMPP guide above

## Using PHP Built-in Server (Any OS)

### Prerequisites
- PHP installed on your system
- MySQL/MariaDB installed and running

### Steps

```bash
# 1. Navigate to project directory
cd attendancesql

# 2. Create database (using MySQL CLI)
mysql -u root -p
# Enter password, then:
CREATE DATABASE nsut_attendance;
exit

# 3. Import database
mysql -u root -p nsut_attendance < database.sql

# 4. Start PHP server
php -S localhost:8000

# 5. Open browser
# Visit: http://localhost:8000
```

## Verification Checklist

After installation, verify:

- [ ] Can access login page
- [ ] Can login as teacher (rajesh.kumar@nsut.ac.in / teacher001)
- [ ] Teacher can see subjects
- [ ] Teacher can mark attendance
- [ ] Can login as student (aarav.sharma@nsut.ac.in / pass001)
- [ ] Student can view attendance dashboard

## Common Issues

### "Connection failed" error
- **Solution**: Check MySQL is running and credentials in `config.php` are correct

### "Table doesn't exist" error
- **Solution**: Import `database.sql` file using phpMyAdmin or MySQL CLI

### Blank page after login
- **Solution**: Enable PHP error display in `php.ini` or check Apache error logs

### 404 Not Found
- **Solution**: Ensure project is in correct directory (htdocs/www) and URL is correct

## Quick Test

**Teacher Login:**
```
Email: rajesh.kumar@nsut.ac.in
Password: teacher001
```

**Student Login:**
```
Email: aarav.sharma@nsut.ac.in
Password: pass001
```

## Need Help?

Check the main README.md file for detailed documentation and troubleshooting.
