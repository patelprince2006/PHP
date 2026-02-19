<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Guestbook</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>📖 Guestbook</h2>
  <form action="save.php" method="post">
    <label>Name:</label><br>
    <input type="text" name="name" required><br><br>

    <label>Message:</label><br>
    <textarea name="message" rows="4" cols="40" required></textarea><br><br>

    <button type="submit">Submit</button>
  </form>

  <p><a href="list.php">View Guest Entries →</a></p>
</body>
</html>
