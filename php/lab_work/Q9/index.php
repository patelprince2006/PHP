<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Registration Form</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>🧾 PHP Registration Form</header>

  <main>
    <form action="submit.php" method="post">
      <label for="name">Full Name:</label>
      <input type="text" name="name" id="name" required>

      <label for="email">Email:</label>
      <input type="email" name="email" id="email" required>

      <label for="age">Age:</label>
      <input type="number" name="age" id="age" required min="1">

      <label for="password">Password:</label>
      <input type="password" name="password" id="password" required>

      <button type="submit">Register</button>
    </form>
  </main>

  <footer>Created by: Prince Patel [24CE092]</footer>
</body>
</html>
