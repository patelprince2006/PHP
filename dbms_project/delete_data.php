<?php
  $conn = mysqli_connect("localhost","root","","blog_db") or die("Connection Fails");

  if (!isset($_GET['author']) || !isset($_GET['title'])) {
    header("location: http://localhost/dbms_project/read.php?status=deleted");
    exit;
  }

  $author = mysqli_real_escape_string($conn,$_GET['author']);
  $title = mysqli_real_escape_string($conn,$_GET['title']);
  $sql = "DELETE FROM posts WHERE author = '{$author}' AND title = '{$title}'";
  $squery = mysqli_query($conn, $sql) or die("Error in Query : delete");
  header("location: http://localhost/dbms_project/read.php?status=deleted");
?>

