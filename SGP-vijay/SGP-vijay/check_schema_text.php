<?php
include 'db.php';

function describeTable($conn, $tableName) {
    echo "TABLE: $tableName\n";
    $query = "SELECT column_name, data_type 
              FROM information_schema.columns 
              WHERE table_name = '$tableName'";
    $result = pg_query($conn, $query);
    if ($result) {
        if (pg_num_rows($result) > 0) {
            while ($row = pg_fetch_assoc($result)) {
                echo "  " . $row['column_name'] . " (" . $row['data_type'] . ")\n";
            }
        } else {
            echo "  Table does not exist or has no columns.\n";
        }
    } else {
        echo "  Error describing table: " . pg_last_error($conn) . "\n";
    }
    echo "\n";
}

describeTable($conn, 'buses');
describeTable($conn, 'trains');
describeTable($conn, 'hotels');
?>
