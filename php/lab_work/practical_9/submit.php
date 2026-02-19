
<!DOCTYPE html>
<html>
<head>
    <title>Form Submission</title>
</head>
<body>
    <h2>Submit Your Details</h2>
    <form action="submit.php" method="POST">
        Name: <input type="text" name="name" required><br><br>
        Email: <input type="email" name="email" required><br><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    $name  = $_POST["name"];
    $email = $_POST["email"];

    if (!empty($name) && !empty($email)) {
      
        $file = fopen("data.txt", "a");
        fwrite($file, "Name: $name | Email: $email ".PHP_EOL);
        fclose($file);

        echo "<h3> Data submitted successfully!</h3>";
        echo "<p>Thank you, <b>$name</b>. Your data has been saved.</p>";
    } else {
        echo "<h3> Error: All fields are required.</h3>";
    }
} else {
    echo "<h3>Invalid request</h3>";
}
?>
