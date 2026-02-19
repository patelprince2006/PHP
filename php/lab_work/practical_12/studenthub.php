<?php
$host = "localhost";
$user = "root";     
$pass = "";          
$dbname = "events";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['insert'])) {
    $title   = $_POST['title'];
    $date  = $_POST['date'];
    $location = $_POST['location'];
    $status   = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO students ( title, date, location, status) VALUES (?, ?, ?, ?)");

    $stmt->bind_param( "ssss", $title, $date, $location, $status);
    
    if ($stmt->execute()) {
        echo "<p> New Event inserted successfully!</p>";
    } else {
        echo "<p> Error: " . $stmt->error . "</p>";
    }
}

if (isset($_POST['select'])) {
    $event_id = $_POST['event_id'];

    $stmt = $conn->prepare("SELECT event_id, title, date, location, status FROM students WHERE event_id=?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>Event Record</h2>
           <table border='1'>
                <tr>
                    <th>ID</th><th>title</th><th>date</th><th>location</th><th>status/th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['event_id']."</td>
                    <td>".$row['title']."</td>
                    <td>".$row['date']."</td>
                    <td>".$row['location']."</td>
                    <td>".$row['status']."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No students found!";
    }
}

 if (isset($_POST['delete'])) {
    $event_id = $_POST['event_id'];
    
    $stmt = $conn->prepare("DELETE FROM students WHERE event_id=?"); 

    $stmt->bind_param( "i",$event_id);
    
    if ($stmt->execute()) {
        echo "<p> student deleted successfully!</p>";
    } else {
        echo "<p> Error: " . $stmt->error . "</p>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub Portal</title>
</head>
<body>
    <h1> Events Detail Portal</h1>

    <h2>Add Event</h2>
    <form method="POST">
        title: <input type="text" name="title" required><br><br>
        date: <input type="text" name="date" required><br><br>
        location: <input type="text" name="location" required><br><br>
        status: <input type="text" name="status" required><br><br>
        <input type="submit" name="insert" value="Add Student">
    </form>
    <hr>

     <h2> Select Event</h2>
    <form method="POST">
       Event ID: <input type="text" name="event_id"><br><br>
        <input type="submit" name="select" value="select student">
    </form>
    <hr>

    <h2>Delete Event</h2>
    <form method="POST">
        Event ID: <input type="text" name="event_id"><br><br>
        <input type="submit" name="delete" value="delete student">
    </form>
    <hr>
</body>
</html>
