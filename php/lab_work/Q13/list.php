
<?php
include "config.php"; // assumes $conn is defined here

$sql = "SELECT id, name, email, branch FROM student ORDER BY id ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h3>Student List</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Branch</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["branch"]) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "No student records found.";
}
?>


