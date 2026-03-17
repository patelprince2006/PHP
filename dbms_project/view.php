<?php include 'header_nav.php' ?>
<section class="page">
  <div class="page-head">
    <h1>Post Details</h1>
    <p class="page-subtitle">Full content for the selected post.</p>
  </div>
  <div class="panel">
    <?php
      $conn = mysqli_connect("localhost","root","","blog_db") or die("Connection is not Build Successfully");
      $id = isset($_GET['id']) ? mysqli_real_escape_string($conn,$_GET['id']) : '';

      if ($id === '') {
        echo "<p class='empty'>Invalid post ID.</p>";
      } else {
        $sql = "SELECT * FROM posts WHERE id = '{$id}'";
        $squery = mysqli_query($conn, $sql) or die("Error in Query : select");

        if(mysqli_num_rows($squery) > 0){
          $row = mysqli_fetch_assoc($squery);
    ?>
      <div class="detail-card">
        <h2><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
        <div class="detail-meta">
          <span>Author: <?php echo htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'); ?></span>
          <span>Category: <?php echo htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8'); ?></span>
          <span>Status: <?php echo htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8'); ?></span>
          <span>Created: <?php echo htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <div class="detail-content">
          <?php echo nl2br(htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8')); ?>
        </div>
      </div>
      <a class="btn" href="blog.php">Back to Read</a>
    <?php
        } else {
          echo "<p class='empty'>Sorry, Record Not Found!</p>";
        }
      }
    ?>
  </div>
</section>
</main>
<footer class="footer">
  <span>DBMS CRUD Blog - Simple and clear CRUD navigation.</span>
</footer>
</body>
</html>
