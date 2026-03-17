<?php include 'header_nav.php' ?>
<section class="page">
  <div class="page-head">
    <h1>Delete Post</h1>
    <p class="page-subtitle">Remove a post by entering its author and title.</p>
  </div>
  <div class="panel">
    <?php
       if(isset($_POST['delete'])){
         $conn = mysqli_connect("localhost","root","","blog_db") or die("Connection is not build Successfully");

         $author = mysqli_real_escape_string($conn,$_POST['author']);
         $title = mysqli_real_escape_string($conn,$_POST['title']);

         $sql = "SELECT * FROM posts WHERE author = '{$author}' AND title = '{$title}'";
         $squery = mysqli_query($conn, $sql) or die("Error in Query : select");

         if(mysqli_num_rows($squery) > 0){
           $sql1 = "DELETE FROM posts WHERE author = '{$author}' AND title = '{$title}'";
           $squery1 = mysqli_query($conn, $sql1) or die("Error in Query : delete");

           header("location: http://localhost/dbms_project/blog.php?status=deleted");
         }else{
           echo "<p class='empty'>Sorry, Record Not Found!</p>";
         }
       }
    ?>
    <form class="form-inline" action="<?php $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="off">
      <div class="f1">
        <label for="">Author</label>
        <input type="text" name="author" required>
      </div>
      <div class="f1">
        <label for="">Title</label>
        <input type="text" name="title" required>
      </div>
      <input type="submit" name="delete" value="Delete" class="submit danger">
    </form>
  </div>
</section>
</main>
<footer class="footer">
  <span>DBMS CRUD Blog - Simple and clear CRUD navigation.</span>
</footer>
</body>
</html>


