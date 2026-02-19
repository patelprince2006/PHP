<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<!-- swiper  -->
<!-- Link Swiper's CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="style.css">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light" class="navbar">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="#">
            <img src="w/flipkart.png" alt="Logo">
        </a>

        <!-- Navbar Toggler for Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" class="btn"
            onclick="showHide()">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <form class="d-flex" action="#" method="get">
                        <div class="input-group">
                            <span class="search-icon"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Search...">
                        </div>
                    </form>
                </li>
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="gallery.php">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>

                <!-- <li class="nav-item">
                        <form class="d-flex" action="#" method="get">
                            <div class="input-group">
                                <span class="search-icon"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Search...">
                            </div>
                        </form>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-login" href="#">
                            <i class="fas fa-user-circle icon" style="color: black;"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-shopping-cart icon"></i> Cart
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-become-seller" href="#"><i class="fa-solid fa-store"></i>Become a Seller</a>
                    </li>
                    <li class="nav-item notifications">
                        <a class="nav-link" href="#">
                            <i class="fas fa-bell icon"></i>
                            <span class="badge">1</span>
                        </a>
                    </li> -->
            </ul>
        </div>
    </div>
</nav>
<script src="index.js"></script>