<?php
include 'db.php';

function describeTable($conn, $tableName) {
    echo "<h3>Table: $tableName</h3>";
    $query = "SELECT column_name, data_type 
              FROM information_schema.columns 
              WHERE table_name = '$tableName'";
    $result = pg_query($conn, $query);
    if ($result) {
        if (pg_num_rows($result) > 0) {
            echo "<table border='1'><tr><th>Column</th><th>Type</th></tr>";
            while ($row = pg_fetch_assoc($result)) {
                echo "<tr><td>" . $row['column_name'] . "</td><td>" . $row['data_type'] . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "Table does not exist or has no columns.";
        }
    } else {
        echo "Error describing table: " . pg_last_error($conn);
    }
}

describeTable($conn, 'buses');
describeTable($conn, 'trains');
describeTable($conn, 'hotels');
?>
