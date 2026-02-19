<?php
$name = trim(htmlspecialchars($_POST['name'] ?? ''));
$message = trim(htmlspecialchars($_POST['message'] ?? ''));

if ($name === '' || $message === '') {
    die("⚠️ Please fill out all fields. <a href='guestbook.php'>Go back</a>");
}

$file = 'guestbook.txt';
$entry = date("Y-m-d H:i:s") . " | " . $name . " | " . $message . PHP_EOL;

if ($fp = fopen($file, 'a')) {
    if (flock($fp, LOCK_EX)) {       
        fwrite($fp, $entry);
        fflush($fp);
        flock($fp, LOCK_UN);       
    }
    fclose($fp);
}

echo "<h3>✅ Thank you, $name! Your entry has been saved.</h3>";
echo "<a href='guestbook.php'>← Back</a> | <a href='list.php'>View Entries</a>";
?>
