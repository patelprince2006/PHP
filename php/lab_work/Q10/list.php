<?php
$file = 'guestbook.txt';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Guestbook Entries</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>📋 Guestbook Entries</h2>

  <?php
  if (file_exists($file)) {
      $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      if (count($lines) > 0) {
          echo "<ul>";
          foreach (array_reverse($lines) as $line) { // newest first
              [$date, $name, $message] = explode(" | ", $line);
              echo "<li><strong>$name</strong> <em>($date)</em><br>$message</li><hr>";
          }
          echo "</ul>";
      } else {
          echo "<p>No entries yet.</p>";
      }
  } else {
      echo "<p>No entries yet.</p>";
  }
  ?>

  <p><a href="guestbook.php">← Back to form</a></p>
</body>
</html>
