<?php

include "config.php";

if (isset($_POST["update"])) {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $branch = $_POST["branch"];

    $stmt = $conn->prepare("UPDATE student SET name = ?, email = ?, branch = ? WHERE id = ?");
    $stmt->bind_param("isss", $id,$name, $email, $branch);

    if ($stmt->execute()) {
        echo "<br><h3>Student data updated successfully</h3>";
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
    <title>Document</title>
</head>
<body>

<form action="edit.php" method="POST">
    id: <input type="number" name="id" required><br><br>
    Name: <input type="text" name="name"  required><br><br>
    Email: <input type="text" name="email" required><br><br>
    Branch: <input type="text" name="branch" required><br><br>
    <button type="submit" name="update">Update</button>
</form>

    
</body>
</html>