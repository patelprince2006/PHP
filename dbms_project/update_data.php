<?php
  $conn = mysqli_connect("localhost","root","","blog_db") or die("Connection is not build Successfully");
  $original_author = mysqli_real_escape_string($conn,$_POST['original_author']);
  $original_title = mysqli_real_escape_string($conn,$_POST['original_title']);
  $title = mysqli_real_escape_string($conn,$_POST['title']);
  $content_raw = $_POST['content'];
  $content = mysqli_real_escape_string($conn,$content_raw);
  $author = mysqli_real_escape_string($conn,$_POST['author']);
  $category = mysqli_real_escape_string($conn,$_POST['category']);
  $status = mysqli_real_escape_string($conn,$_POST['status']);

  $word_count = str_word_count(strip_tags($content_raw));
  if ($word_count < 30) {
    echo "<p>Content must be at least 30 words. Current: {$word_count}.</p>";
    echo "<p><a href='javascript:history.back()'>Go back</a></p>";
    exit;
  }

  $sql = "UPDATE posts SET title = '{$title}', content = '{$content}', author = '{$author}', category = '{$category}', status = '{$status}'
          WHERE author = '{$original_author}' AND title = '{$original_title}'";
  $squery = mysqli_query($conn, $sql) or die("Error in query : update");
  header("location: http://localhost/dbms_project/blog.php?status=updated");
?>
