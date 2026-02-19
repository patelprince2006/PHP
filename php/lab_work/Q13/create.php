<?php

include "config.php";

if (isset($_POST["insert"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $branch = $_POST["branch"];

    $stmt = $conn->prepare("INSERT INTO student(name, email, branch) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $branch);

    if ($stmt->execute()) {
        echo "<br><h4>Student data inserted successfully</h4>";
    } else {
        echo "Error: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create</title>
</head>
<body>
    <h3>Student Data Insert</h3>
    <form action="create.php" method="POST">
      Name: <input type="text" placeholder="name" name="name" required><br><br>
      email: <input type="email" placeholder="email" name="email" required><br><br>
      branch: <input type="text" placeholder="branch" name="branch" required><br><br>
      
    <button type="submit" name="insert"> add data </button>
    </form>
</body>
</html>