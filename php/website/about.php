<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <main>
        <div class="video-container">
            <!-- Video Player -->
            <video controls autoplay muted poster="thumbnail.jpg" class="responsive-video">
                <source src="w/video.mp4" type="video/mp4">
            </video>
        </div>

        <section class="banner">
            <h1>Welcome to AboutUs</h1>
            <p>Discover amazing products at unbeatable prices!</p>
            <a href="gallery.php" class="btn">Shop Now</a>
        </section>

        <section class="products">
            <h2>Top Deals</h2>
            <div class="product-grid">
                <div class="product">
                    <img src="w/g8.jpg" alt="Product 1">
                    <h3>Product 1</h3>
                    <p>$100</p>
                </div>
                <div class="product">
                    <img src="w/g9.jpg" alt="Product 2">
                    <h3>Product 2</h3>
                    <p>$150</p>
                </div>
                <div class="product">
                    <img src="w/g8.jpg" alt="Product 3">
                    <h3>Product 3</h3>
                    <p>$200</p>
                </div>
                <div class="product">
                    <img src="w/g9.jpg" alt="Product 4">
                    <h3>Product 4</h3>
                    <p>$250</p>
                </div>
            </div>
        </section>
        <section class="team-section">
            <h1>Meet Our Team</h1>
            <div class="team-grid">
                <div class="team-member">
                    <img src="w/a1.png" alt="Team Member 1">
                    <h2>John Doe</h2>
                    <p class="role">CEO</p>
                    <p>John is the visionary leader of our company, driving innovation and strategy.</p>
                </div>
                <div class="team-member">
                    <img src="w/a2.png" alt="Team Member 2">
                    <h2>Jane Smith</h2>
                    <p class="role">CTO</p>
                    <p>Jane leads our tech team, ensuring seamless solutions and innovation.</p>
                </div>
                <div class="team-member">
                    <img src="w/a3.png" alt="Team Member 3">
                    <h2>Emily Johnson</h2>
                    <p class="role">Designer</p>
                    <p>Emily crafts user-friendly designs to create delightful experiences.</p>
                </div>
                <div class="team-member">
                    <img src="w/a4.png" alt="Team Member 4">
                    <h2>Mark Lee</h2>
                    <p class="role">Marketing Head</p>
                    <p>Mark is the brain behind our marketing campaigns and brand growth.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="index.js"></script>
    <?php include 'footer.php'; ?>
</body>

</html>