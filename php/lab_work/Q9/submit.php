<?php
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$age = $_POST['age'] ?? '';
$password = $_POST['password'] ?? '';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($age)) {
        $errors['age'] = "Age is required.";
    } elseif ($age < 17) {
        $errors['age'] = "You must be at least 17 years old.";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters.";
    }

    if (!empty($errors)) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <title>Form Validation</title>
          <link rel="stylesheet" href="style.css">
        </head>
        <body>
          <header>🧾 PHP Registration Form (Validation Errors)</header>
          <main>
            <form action="submit.php" method="post">
              <label for="name">Full Name:</label>
              <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>">
              <span class="error"><?= $errors['name'] ?? '' ?></span>

              <label for="email">Email:</label>
              <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>">
              <span class="error"><?= $errors['email'] ?? '' ?></span>

              <label for="age">Age:</label>
              <input type="number" name="age" id="age" value="<?= htmlspecialchars($age) ?>">
              <span class="error"><?= $errors['age'] ?? '' ?></span>

              <label for="password">Password:</label>
              <input type="password" name="password" id="password">
              <span class="error"><?= $errors['password'] ?? '' ?></span>

              <button type="submit">Register</button>
            </form>
          </main>
          <footer>Created by: Prince Patel [24CE092]</footer>
        </body>
        </html>
        <?php
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8">
          <title>Registration Successful</title>
          <link rel="stylesheet" href="style.css">
        </head>
        <body>
          <header>🎉 Registration Successful</header>
          <div class="success">
            <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Age:</strong> <?= htmlspecialchars($age) ?></p>
            <p>Your registration has been submitted successfully!</p>
          </div>
          <footer>Created by: Prince Patel [24CE092]</footer>
        </body>
        </html>
        <?php
    }
}
?>
