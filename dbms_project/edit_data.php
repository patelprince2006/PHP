<?php include 'header_nav.php' ?>
<section class="page">
  <div class="page-head">
    <h1>Edit Post</h1>
    <p class="page-subtitle">Update a blog post by author and title.</p>
  </div>
  <div class="panel">
    <?php
       $conn = mysqli_connect("localhost","root","","blog_db") or die("Connection Fails");
       $error = "";
       $form = null;

       function e($value) {
         return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
       }

       $author_param = isset($_GET['author']) ? mysqli_real_escape_string($conn,$_GET['author']) : '';
       $title_param = isset($_GET['title']) ? mysqli_real_escape_string($conn,$_GET['title']) : '';

       if ($author_param === '' || $title_param === '') {
         echo "<p class='empty'>Missing author or title.</p>";
       } else {
         $sql = "SELECT * FROM posts WHERE author = '{$author_param}' AND title = '{$title_param}' ORDER BY created_at DESC LIMIT 1";
         $squery = mysqli_query($conn, $sql) or die("Error in Query : select1");

         if(isset($_POST['save'])){
           $original_author = mysqli_real_escape_string($conn,$_POST['original_author']);
           $original_title = mysqli_real_escape_string($conn,$_POST['original_title']);
           $title = mysqli_real_escape_string($conn,$_POST['title']);
           $content_raw = $_POST['content'];
           $content = mysqli_real_escape_string($conn,$content_raw);
           $author = mysqli_real_escape_string($conn,$_POST['author']);
           $category = mysqli_real_escape_string($conn,$_POST['category']);
           $status = mysqli_real_escape_string($conn,$_POST['status']);

           $word_count = str_word_count(strip_tags($content_raw));
           if ($word_count < 300) {
             $error = "Content must be at least 300 words. Current: {$word_count}.";
             $form = [
               "title" => $_POST['title'],
               "content" => $content_raw,
               "author" => $_POST['author'],
               "category" => $_POST['category'],
               "status" => $_POST['status']
             ];
           } else {
             $sql2 = "UPDATE posts SET title = '{$title}', content = '{$content}', author = '{$author}', category = '{$category}', status = '{$status}'
                      WHERE author = '{$original_author}' AND title = '{$original_title}'";

             $squery2 = mysqli_query($conn, $sql2) or die("Error in query : update");

             header("location: http://localhost/dbms_project/read.php?status=updated");
           }
         }

         if(mysqli_num_rows($squery) > 0){
           $row = mysqli_fetch_assoc($squery);
           $current = $row;
           if ($form) {
             $current = array_merge($row, $form);
           }
    ?>
    <?php if(!empty($error)){ ?>
      <p class="error"><?php echo e($error); ?></p>
    <?php } ?>
    <form class="form-grid" action="<?php $_SERVER['PHP_SELF']; ?>?author=<?php echo urlencode($row['author']); ?>&title=<?php echo urlencode($row['title']); ?>" method="post" autocomplete="off">
      <input type="hidden" name="original_author" value="<?php echo e($row['author']); ?>">
      <input type="hidden" name="original_title" value="<?php echo e($row['title']); ?>">
      <div class="f1">
        <label for="">Title</label>
        <input type="text" name="title" value="<?php echo e($current['title']); ?>" required>
      </div>
      <div class="f1">
        <label for="">Content</label>
        <textarea name="content" rows="8" required><?php echo e($current['content']); ?></textarea>
        <p class="helper">Minimum 300 words required.</p>
      </div>
      <div class="f1">
        <label for="">Author</label>
        <input type="text" name="author" value="<?php echo e($current['author']); ?>" required>
      </div>
      <div class="f1">
        <label for="">Category</label>
        <input type="text" name="category" value="<?php echo e($current['category']); ?>" required>
      </div>
      <div class="f1">
        <label for="">Status</label>
        <select name="status" required>
          <option value="draft" <?php echo ($current['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
          <option value="published" <?php echo ($current['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
        </select>
      </div>
      <input type="submit" name="save" value="Save" class="submit">
    </form>
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

