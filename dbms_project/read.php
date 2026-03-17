<?php include 'header_nav.php'; ?>
<section class="page">
  <div class="page-head">
    <h1>Read Posts</h1>
    <p class="page-subtitle">Choose an author or category to view full post details.</p>
  </div>
  <div class="panel">
    <?php
       $conn = mysqli_connect("localhost","root","","blog_db") or die("Connection is not Build Successfully");

       function e($value) {
         return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
       }

       if (isset($_GET['status'])) {
         $status = $_GET['status'];
         if ($status === 'updated') {
           echo "<script>alert('Post updated successfully.');</script>";
         } elseif ($status === 'deleted') {
           echo "<script>alert('Post deleted successfully.');</script>";
         }
         elseif ($status === 'created') {
           echo "<script>alert('Post created successfully.');</script>";
         }
       }

       $authors = [];
       $categories = [];

       $author_query = mysqli_query($conn, "SELECT DISTINCT author FROM posts ORDER BY author");
       if ($author_query) {
         while ($row = mysqli_fetch_assoc($author_query)) {
           $authors[] = $row['author'];
         }
       }

       $category_query = mysqli_query($conn, "SELECT DISTINCT category FROM posts ORDER BY category");
       if ($category_query) {
         while ($row = mysqli_fetch_assoc($category_query)) {
           $categories[] = $row['category'];
         }
       }

       $author_filter = isset($_GET['author']) ? trim($_GET['author']) : '';
       $category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';

       $show_results = ($author_filter !== '' || $category_filter !== '');

       if (isset($_GET['page'])) {
         $page_number = (int)$_GET['page'];
       } else {
         $page_number = 1;
       }
       $limit = 6;
       $offset = ($page_number - 1) * $limit;

       $filters = [];
       if ($author_filter !== '') {
         $author_esc = mysqli_real_escape_string($conn, $author_filter);
         $filters[] = "author = '{$author_esc}'";
       }
       if ($category_filter !== '') {
         $category_esc = mysqli_real_escape_string($conn, $category_filter);
         $filters[] = "category = '{$category_esc}'";
       }
       $where = count($filters) ? "WHERE " . implode(" AND ", $filters) : "";
    ?>

    <form class="filter-form" method="get" action="read.php">
      <div class="f1">
        <label for="author">Author</label>
        <select name="author">
          <option value="">All Authors</option>
          <?php foreach ($authors as $author) { ?>
            <option value="<?php echo e($author); ?>" <?php echo ($author === $author_filter) ? 'selected' : ''; ?>>
              <?php echo e($author); ?>
            </option>
          <?php } ?>
        </select>
      </div>
      <div class="f1">
        <label for="category">Category</label>
        <select name="category">
          <option value="">All Categories</option>
          <?php foreach ($categories as $category) { ?>
            <option value="<?php echo e($category); ?>" <?php echo ($category === $category_filter) ? 'selected' : ''; ?>>
              <?php echo e($category); ?>
            </option>
          <?php } ?>
        </select>
      </div>
      <input type="submit" value="Filter" class="submit">
    </form>

    <?php
       if (!$show_results) {
         echo "<p class='empty'>Please choose an author or category to view posts.</p>";
       } else {
         $sql = "SELECT * FROM posts {$where} ORDER BY created_at DESC LIMIT {$offset}, {$limit}";
         $squery = mysqli_query($conn, $sql) or die("Error in Query : select1");

         if (mysqli_num_rows($squery) > 0) {
    ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Category</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
            while ($row = mysqli_fetch_assoc($squery)) {
              $author_param = urlencode($row['author']);
              $title_param = urlencode($row['title']);
          ?>
          <tr>
            <td><?php echo e($row['title']); ?></td>
            <td><?php echo e($row['author']); ?></td>
            <td><?php echo e($row['category']); ?></td>
            <td><?php echo e($row['status']); ?></td>
            <td><?php echo e($row['created_at']); ?></td>
            <td class="actions">
              <a href="view.php?id=<?php echo e($row['id']); ?>" class="pill-link">View</a>
              <a href="edit_data.php?author=<?php echo $author_param; ?>&title=<?php echo $title_param; ?>" class="pill-link edit">Edit</a>
              <a href="delete_data.php?author=<?php echo $author_param; ?>&title=<?php echo $title_param; ?>" class="pill-link delete">Delete</a>
            </td>
          </tr>
          <?php
               }
          ?>
        </tbody>
      </table>
    </div>
    <?php
         } else {
           echo "<p class='empty'>Sorry, Record Not Found!</p>";
         }

         $count_sql = "SELECT COUNT(*) AS total FROM posts {$where}";
         $count_query = mysqli_query($conn, $count_sql) or die("Error in Query : select2");
         $count_row = mysqli_fetch_assoc($count_query);
         $total_record = (int)$count_row['total'];
         $total_pages = ceil($total_record / $limit);

         $query_params = [];
         if ($author_filter !== '') {
           $query_params['author'] = $author_filter;
         }
         if ($category_filter !== '') {
           $query_params['category'] = $category_filter;
         }
    ?>
    <div class="pagination">
      <?php
          if ($page_number > 1) {
            $query_params['page'] = $page_number - 1;
            echo '<a href="read.php?'.http_build_query($query_params).'">Prev</a>';
          }
          for ($i = 1; $i <= $total_pages; $i++) {
            $query_params['page'] = $i;
            $active = ($page_number == $i) ? "active" : "";
            echo 'page: <a href="read.php?'.http_build_query($query_params).'" class='.$active.'>'.$i.'</a>';
          }
          if ($page_number < $total_pages) {
            $query_params['page'] = $page_number + 1;
            echo '<a href="read.php?'.http_build_query($query_params).'">Next</a>';
          }
      ?>
    </div>
    <?php } ?>
  </div>
</section>
</main>
<footer class="footer">
  <span>DBMS CRUD Blog - Simple and clear CRUD navigation.</span>
</footer>
</body>
</html>





