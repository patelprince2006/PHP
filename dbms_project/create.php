<?php include 'header_nav.php' ?>
<section class="page">
  <div class="page-head">
    <h1>Create Post</h1>
    <p class="page-subtitle">Add a new blog post to the database.</p>
  </div>
  <div class="panel">
    <?php
       $conn = mysqli_connect("localhost","root","","blog_db") or die("Connection is Not Build Successfully");
       $error = "";

       function e($value) {
         return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
       }

       if(isset($_POST['save'])){
         $title = mysqli_real_escape_string($conn,$_POST['title']);
         $content_raw = $_POST['content'];
         $content = mysqli_real_escape_string($conn,$content_raw);
         $author = mysqli_real_escape_string($conn,$_POST['author']);
         $category = mysqli_real_escape_string($conn,$_POST['category']);
         $status = mysqli_real_escape_string($conn,$_POST['status']);

         $word_count = str_word_count(strip_tags($content_raw));
         if ($word_count < 30) {
           $error = "Content must be at least 30 words. Current: {$word_count}.";
         } else {
           $sql1 = "INSERT INTO posts(title,content,author,category,status)
                    VALUES ('{$title}','{$content}','{$author}','{$category}','{$status}')";
           $squery1 = mysqli_query($conn, $sql1) or die("Error in Query : insert");

           header("location: http://localhost/dbms_project/blog.php?status=created");
         }
       }
    ?>
    <?php if(!empty($error)){ ?>
      <p class="error"><?php echo e($error); ?></p>
    <?php } ?>
    <form class="form-grid" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
      <div class="f1">
        <label for="title">Title</label>
        <input type="text" name="title" value="<?php echo isset($title) ? e($title) : ''; ?>" required>
      </div>
      <div class="f1">
        <label for="content">Content</label>
        <textarea name="content" rows="8" required><?php echo isset($content_raw) ? e($content_raw) : ''; ?></textarea>
        <p class="helper">Minimum 30 words required.</p>
      </div>
      <div class="f1">
        <label for="author">Author</label>
        <input type="text" name="author" value="<?php echo isset($author) ? e($author) : ''; ?>" required>
      </div>
      <div class="f1">
        <label for="category">Category</label>
        <input type="text" name="category" value="<?php echo isset($category) ? e($category) : ''; ?>" required>
      </div>
      <div class="f1">
        <label for="status">Status</label>
        <select name="status" required>
          <option value="draft" <?php echo (isset($status) && $status === 'draft') ? 'selected' : ''; ?>>Draft</option>
          <option value="published" <?php echo (isset($status) && $status === 'published') ? 'selected' : ''; ?>>Published</option>
        </select>
      </div>
      <input type="submit" name="save" value="Save" class="submit">
    </form>
  </div>
</section>
</main>
<footer class="footer">
  <span>DBMS CRUD Blog - Simple and clear CRUD navigation.</span>
</footer>
</body>
</html>


