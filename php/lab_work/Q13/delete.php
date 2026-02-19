 <?php
 include "config.php";
 if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    
    $stmt = $conn->prepare("DELETE FROM student WHERE id=?"); 

    $stmt->bind_param( "i",$id);
    
    if ($stmt->execute()) {
        echo "<p> student deleted successfully!</p>";
    } else {
        echo "<p> Error: " . $stmt->error . "</p>";
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

<form action="delete.php" method="POST">
    id: <input type="number" name="id" required><br><br>
    <button type="submit" name="delete">Delete</button>
</form>

    
</body>
</html>