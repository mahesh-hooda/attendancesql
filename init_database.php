<?php
// ===============================================
// SQLite Database Initialization Script
// Run this file to create and populate the database
// ===============================================

require_once 'config.php';

echo "Starting database initialization...\n\n";

try {
    // Get database connection
    $conn = getDbConnection();

    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/database.sql');

    if ($sql === false) {
        die("Error: Could not read database.sql file\n");
    }

    // Remove comments and split into individual statements
    $sql = preg_replace('/--.*$/m', '', $sql); // Remove single-line comments
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt);
        }
    );

    echo "Found " . count($statements) . " SQL statements to execute\n\n";

    $success_count = 0;
    $error_count = 0;

    // Execute each statement
    foreach ($statements as $index => $statement) {
        if (empty(trim($statement))) {
            continue;
        }

        try {
            $conn->exec($statement);
            $success_count++;

            // Show progress for important operations
            if (stripos($statement, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE\s+(\w+)/i', $statement, $matches);
                $table = $matches[1] ?? 'unknown';
                echo "✓ Created table: $table\n";
            } elseif (stripos($statement, 'CREATE VIEW') !== false) {
                preg_match('/CREATE VIEW\s+(\w+)/i', $statement, $matches);
                $view = $matches[1] ?? 'unknown';
                echo "✓ Created view: $view\n";
            } elseif (stripos($statement, 'INSERT INTO') !== false) {
                preg_match('/INSERT INTO\s+(\w+)/i', $statement, $matches);
                $table = $matches[1] ?? 'unknown';
                // Count how many value sets
                $value_count = substr_count($statement, '),(') + 1;
                if ($value_count > 1) {
                    echo "✓ Inserted $value_count rows into $table\n";
                }
            } elseif (stripos($statement, 'DROP TABLE') !== false) {
                preg_match('/DROP TABLE.*?(\w+)/i', $statement, $matches);
                $table = $matches[1] ?? 'unknown';
                echo "✓ Dropped table: $table\n";
            }

        } catch (PDOException $e) {
            $error_count++;
            echo "✗ Error executing statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
            echo "Statement: " . substr($statement, 0, 100) . "...\n\n";
        }
    }

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "Database initialization complete!\n";
    echo "Successful statements: $success_count\n";
    echo "Failed statements: $error_count\n";
    echo str_repeat("=", 50) . "\n\n";

    // Verify tables were created
    echo "Verifying database structure...\n";
    $tables = $conn->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables created: " . implode(", ", $tables) . "\n\n";

    // Show some statistics
    $stats = [
        'students' => $conn->query("SELECT COUNT(*) FROM students")->fetchColumn(),
        'teachers' => $conn->query("SELECT COUNT(*) FROM teachers")->fetchColumn(),
        'subjects' => $conn->query("SELECT COUNT(*) FROM subjects")->fetchColumn(),
        'attendance' => $conn->query("SELECT COUNT(*) FROM attendance")->fetchColumn(),
    ];

    echo "Data Statistics:\n";
    echo "- Students: {$stats['students']}\n";
    echo "- Teachers: {$stats['teachers']}\n";
    echo "- Subjects: {$stats['subjects']}\n";
    echo "- Attendance records: {$stats['attendance']}\n";

    echo "\n✓ Database is ready to use!\n";
    echo "Database file location: " . DB_PATH . "\n";

} catch (Exception $e) {
    echo "✗ Fatal error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
