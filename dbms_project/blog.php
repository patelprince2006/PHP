<?php
$site = [
  "name" => "Blog",
  "tagline" => "Simple CRUD navigation",
  "status" => "All systems normal"
];

$stats = [
  "posts" => 0,
  "updated" => "No posts yet"
];

$actions = [
  ["label" => "Read", "desc" => "See all posts and details.", "link" => "read.php"],
  ["label" => "Create", "desc" => "Add a new blog post.", "link" => "create.php"],
  ["label" => "Update", "desc" => "Edit an existing post.", "link" => "update.php"],
  ["label" => "Delete", "desc" => "Remove a post safely.", "link" => "delete.php"]
];

$conn = mysqli_connect("localhost","root","","blog_db") or die("Connection is not Build Successfully");
$stats_sql = "SELECT COUNT(*) AS total, MAX(created_at) AS last_updated FROM posts";
$stats_query = mysqli_query($conn, $stats_sql);
if ($stats_query) {
  $stats_row = mysqli_fetch_assoc($stats_query);
  $stats["posts"] = (int)$stats_row["total"];
  if (!empty($stats_row["last_updated"])) {
    $stats["updated"] = date("M d, Y", strtotime($stats_row["last_updated"]));
  }
}
mysqli_close($conn);

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
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($site["name"]); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="blog.css">
  </head>
  <body>
    <header class="header">
      <div class="logo"><?php echo e($site["name"]); ?></div>
      <nav class="nav">
        <a href="read.php">Read</a>
        <a href="create.php">Create</a>
        <a href="update.php">Update</a>
        <a href="delete.php">Delete</a>
      </nav>
    </header>

    <main class="container">
      <section class="hero">
        <div>
          <p class="tag">Blog Dashboard</p>
          <h1>blog posts with simple CRUD actions.</h1>
          <p class="subtitle">
            Use the buttons below to view, add, edit, or remove posts. Each link goes to the
            working CRUD pages in this project.
          </p>
          <div class="hero-actions">
            <a class="btn primary" href="read.php">View Posts</a>
            <a class="btn" href="create.php">Add New Post</a>
          </div>
        </div>
        <div class="hero-panel">
          <div class="panel-row">
            <span>Total Posts</span>
            <strong><?php echo e($stats["posts"]); ?></strong>
          </div>
          <div class="panel-row">
            <span>Last Updated</span>
            <strong><?php echo e($stats["updated"]); ?></strong>
          </div>
          <div class="panel-row">
            <span>Status</span>
            <strong><?php echo e($site["status"]); ?></strong>
          </div>
        </div>
      </section>

      <section class="crud-grid">
        <?php foreach ($actions as $action) { ?>
        <a class="crud-card" href="<?php echo e($action["link"]); ?>">
          <h2><?php echo e($action["label"]); ?></h2>
          <p><?php echo e($action["desc"]); ?></p>
          <span>Go to <?php echo e($action["label"]); ?></span>
        </a>
        <?php } ?>
      </section>

      <section class="tips">
        <h3>Quick Tips</h3>
        <ul>
          <li>Use Read first to find the correct ID.</li>
          <li>Create adds a new post to the database.</li>
          <li>Update edits an existing post by ID.</li>
          <li>Delete removes a post permanently.</li>
        </ul>
      </section>
    </main>

    <footer class="footer">
      <span><?php echo e($site["tagline"]); ?></span>
    </footer>
  </body>
</html>
