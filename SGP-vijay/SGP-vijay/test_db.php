<?php
// Include the database connection file
require_once 'db.php';

echo "<h2>Testing Database Connection...</h2>";

if ($conn) {
    echo "<p style='color:green;'>Connection Successful!</p>";
    
    // Query to get all tables in the public schema
    $query = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'";
    $result = pg_query($conn, $query);

    if (!$result) {
        echo "An error occurred.\n";
        exit;
    }

    echo "<h3>Tables in 'public' schema:</h3>";
    echo "<ul>";
    
    $table_count = 0;
    while ($row = pg_fetch_assoc($result)) {
        echo "<li>" . $row['table_name'] . "</li>";
        $table_count++;
    }
    
    if ($table_count == 0) {
        echo "<li>(No tables found)</li>";
    }
    echo "</ul>";

} else {
    echo "<p style='color:red;'>Connection Failed.</p>";
}
?>
