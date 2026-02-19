<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <section class="contact-section">
        <h1>Contact Us</h1>
        <p class="description">We're here to help! Reach out to us for inquiries, support, or feedback.</p>

        <div class="contact-container">
            <!-- Contact Form -->
            <div class="contact-form">
                <h2>Send Us a Message</h2>
                <form action="#" method="POST">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Your Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Write your message here"
                            required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Submit</button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="contact-info">
                <h2>Contact Information</h2>
                <p><strong>Address:</strong> Flipkart Internet Private Limited,<br>
                    Buildings Alyssa, Begonia &<br>
                    Clove Embassy Tech Village,<br>
                    Outer Ring Road, Devarabeesanahalli Village,<br>
                    Bengaluru, 560103,<br>
                    Karnataka, India</p>
                <p><strong>Email:</strong> support@contactus.com</p>
                <p><strong>Phone:</strong> 044-45614700 / 044-67415800</p>
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#" class="social-icon">Facebook</a>
                    <a href="#" class="social-icon">Twitter</a>
                    <a href="#" class="social-icon">Instagram</a>
                </div>
            </div>
        </div>
    </section>
    </div>
    </div>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="index.js"></script>
    <?php include 'footer.php'; ?>
</body>

</html>