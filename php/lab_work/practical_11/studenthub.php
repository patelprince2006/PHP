<?php
$host = "localhost";
$user = "root";     
$pass = "";          
$dbname = "studenthub";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['insert'])) {
    $name   = $_POST['name'];
    $email  = $_POST['email'];
    $course = $_POST['course'];
    $year   = $_POST['year'];
    
    $stmt = $conn->prepare("INSERT INTO students (name, email, course, year) VALUES (?, ?, ?, ?)");

    $stmt->bind_param( "sssi",$name, $email, $course, $year);
    
    if ($stmt->execute()) {
        echo "<p> New student inserted successfully!</p>";
    } else {
        echo "<p> Error: " . $stmt->error . "</p>";
    }
}

if (isset($_POST['select'])) {
    $id = $_POST['student_id'];

    $stmt = $conn->prepare("SELECT student_id, name, email, course, year FROM students WHERE student_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2>Student Record</h2>
           <table border='1'>
                <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>".$row['student_id']."</td>
                    <td>".$row['name']."</td>
                    <td>".$row['email']."</td>
                    <td>".$row['course']."</td>
                    <td>".$row['year']."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No students found!";
    }
}

 if (isset($_POST['delete'])) {
    $student_id = $_POST['student_id'];
    
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id=?"); 

    $stmt->bind_param( "i",$student_id);
    
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
    <h1> StudentHub Portal</h1>

    <h2>Add Student</h2>
    <form method="POST">
       Name: <input type="text" name="name" required><br><br>
        Email: <input type="email" name="email" required><br><br>
        Course: <input type="text" name="course" required><br><br>
        Year: <input type="number" name="year" required><br><br>
        <input type="submit" name="insert" value="Add Student">
    </form>
    <hr>

     <h2> Select Student</h2>
    <form method="POST">
        Student ID: <input type="text" name="student_id"><br><br>
        <input type="submit" name="select" value="select student">
    </form>
    <hr>

    <h2>Delete Students</h2>
    <form method="POST">
        Student ID: <input type="text" name="student_id"><br><br>
        <input type="submit" name="delete" value="delete student">
    </form>
    <hr>
</body>
</html>
