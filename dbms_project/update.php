<?php include 'header_nav.php' ?>
<section class="page">
  <div class="page-head">
    <h1>Update Post</h1>
    <p class="page-subtitle">Find a post by author and title, then update its details.</p>
  </div>
  <div class="panel">
    <form class="form-inline" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
      <div class="f1">
        <label for="">Author</label>
        <input type="text" name="author" value="" required>
      </div>
      <div class="f1">
        <label for="">Title</label>
        <input type="text" name="title" value="" required>
      </div>
      <input type="submit" name="show" value="Show" class="submit">
    </form>
    <?php
       $conn = mysqli_connect("localhost","root","","blog_db") or die("Connection is not build Successfully");

       if(isset($_POST['show'])){
         $author = mysqli_real_escape_string($conn,$_POST['author']);
         $title = mysqli_real_escape_string($conn,$_POST['title']);
         $sql = "SELECT * FROM posts WHERE author = '{$author}' AND title = '{$title}' ORDER BY created_at DESC LIMIT 1";
         $squery = mysqli_query($conn, $sql) or die("Error in Query : select");

         if(mysqli_num_rows($squery) > 0){
           while($row = mysqli_fetch_assoc($squery)){
    ?>
    <form class="form-grid" action="update_data.php" method="post" autocomplete="off">
      <input type="hidden" name="original_author" value="<?php echo $row['author']; ?>">
      <input type="hidden" name="original_title" value="<?php echo $row['title']; ?>">
      <div class="f1">
        <label for="">Title</label>
        <input type="text" name="title" value="<?php echo $row['title']; ?>" required>
      </div>
      <div class="f1">
        <label for="">Content</label>
        <textarea name="content" rows="8" required><?php echo $row['content']; ?></textarea>
        <p class="helper">Minimum 30 words required.</p>
      </div>
      <div class="f1">
        <label for="">Author</label>
        <input type="text" name="author" value="<?php echo $row['author']; ?>" required>
      </div>
      <div class="f1">
        <label for="">Category</label>
        <input type="text" name="category" value="<?php echo $row['category']; ?>" required>
      </div>
      <div class="f1">
        <label for="">Status</label>
        <select name="status" required>
          <option value="draft" <?php echo $row['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
          <option value="published" <?php echo $row['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
        </select>
      </div>
      <input type="submit" name="save" value="Update" class="submit">
    </form>
    <?php
         }
       }else{
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
