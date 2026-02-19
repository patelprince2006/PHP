<?php
require_once 'db_config.php';
session_start();
$userId = $_SESSION['user_id'] ?? null;
$userName = $_SESSION['user_name'] ?? null;

// --- NEW CODE: Capture pending product ID from the URL ---
$pendingAddId = null;
if (isset($_GET['add']) && $userId) {
    // Sanitize the product ID
    $pendingAddId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_GET['add']);
}
// --------------------------------------------------------

// load saved shipping profile if available
$profile = [];
if ($userId) {
  // try to read user_profiles table
  if (isset($mysqli) && $mysqli instanceof mysqli) {
    $stmt = $mysqli->prepare('SELECT name,address,city,state,pincode,mobile FROM user_profiles WHERE user_id = ? LIMIT 1');
    if ($stmt) {
      $stmt->bind_param('i', $userId);
      $stmt->execute();
      $stmt->bind_result($p_name, $p_address, $p_city, $p_state, $p_pincode, $p_mobile);
      if ($stmt->fetch()) {
        $profile = [
          'name' => $p_name,
          'address' => $p_address,
          'city' => $p_city,
          'state' => $p_state,
          'pincode' => $p_pincode,
          'mobile' => $p_mobile
        ];
      }
      $stmt->close();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nutra-Leaf Wellness</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="cart.css?v=<?php echo time(); ?>">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
      /* Basic styles to match the Nutra-Leaf image header/footer */
      /* Navbar - Best UI/UX Design */
      .navbar {
        background: #0d3625;
        padding: 0 40px;
        height: 80px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 100;
      }
      
      .nav-left, .nav-right {
          display: flex;
          align-items: center;
          gap: 30px; /* Uniform spacing */
      }

      /* Uniform styling for all navbar items */
      .nav-item {
        color: #fff;
        text-decoration: none;
        font-size: 16px; /* Uniform size requested */
        font-weight: 500;
        transition: all 0.3s ease;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 5px 0;
        position: relative;
        font-family: inherit;
        display: flex;
        align-items: center;
        gap: 5px;
      }

      /* Hover effect: Underline animation */
      .nav-item::after {
          content: '';
          position: absolute;
          width: 0;
          height: 2px;
          bottom: 0;
          left: 0;
          background-color: #f3dc12;
          transition: width 0.3s;
      }
      
      .nav-item:hover {
          color: #f3dc12;
      }
      
      .nav-item:hover::after {
          width: 100%;
      }

      .logo img {
          height: 50px;
          vertical-align: middle;
          transition: transform 0.3s;
      }
      .logo:hover img {
          transform: scale(1.05);
      }
      
      /* Greeting Style */
      .user-greeting {
          color: #a8d7a1;
          font-size: 16px;
          font-weight: 400;
          padding-left: 10px;
          border-left: 1px solid rgba(255,255,255,0.2);
          margin-left: 10px;
          height: 30px;
          display: flex;
          align-items: center;
      }

      /* Cart Count Badge */
      #cartCount {
          background: #ff4757;
          color: white;
          font-size: 11px;
          font-weight: bold;
          padding: 2px 6px;
          border-radius: 10px;
          position: absolute;
          top: -8px;
          right: -10px;
          min-width: 18px;
          text-align: center;
          box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      }
      .hero {
        /* This section will need a specific background image to truly match the Nutra-Leaf banner */
        background: url('hero-nutraleaf.jpg') center/cover no-repeat;
        min-height: 250px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start; /* Align left */
        color: #fff;
        padding: 0 50px;
        text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        margin: 20px 20px auto;
        margin-top: 10px;
        border-radius: 8px;
        padding-top: 10px;
      }
      .hero h1 {
        color: #fff;
        font-size: 2.5em;
        margin-bottom: 5px;
      }
      .hero p {
         color: #fff;
        font-size: 1.2em;
        margin-bottom: 20px;
      }
      .shop-now-btn {
        background: #90b83e; /* Green button color from image */
        color: #0d3625;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 5px;
        
      }
      .btn { /* Apply button style to checkout button and similar elements */
        background: #90b83e; 
        color: #0d3625;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
      }

      .btn :hover{
           color: #f3dc12ff;
      }
      footer {
        background: #0d3625; /* Dark Green footer */
        color: #a8d7a1;
        padding: 20px 18px;
        text-align: center;
      }
      /* Additional footer links based on image */
      .footer-links {
          display: flex;
          justify-content: space-around;
          max-width: 1100px;
          margin: 0 auto 15px;
          text-align: left;
      }
      .footer-links div {
          padding: 0 10px;
      }
      .footer-links h4 {
          color: #fff;
          margin-bottom: 10px;
          font-size: 1.1em;
      }
      .footer-links a {
          display: block;
          color: #a8d7a1;
          text-decoration: none;
          margin-bottom: 5px;
          font-size: 0.9em;
      }
      .footer-links a:hover {
          color: #fff;
      }
      .footer-copyright {
          border-top: 1px solid #1a4d36;
          padding-top: 10px;
      }

      /* --- NEW CART MODAL STYLES (Center Card Design) --- */
      .cart-modal-overlay {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(13, 36, 25, 0.7); /* Darker overlay */
          backdrop-filter: blur(5px);
          display: none;
          justify-content: center;
          align-items: center;
          z-index: 1000;
          opacity: 0;
          transition: opacity 0.3s ease;
      }
      .cart-modal-overlay.active {
          display: flex;
          opacity: 1;
      }
      
      .cart-modal-content {
          width: 100%;
          max-width: 500px; /* Narrower, card-like width */
          max-height: 90vh; /* Allow scrolling */
          background: #fff;
          border-radius: 12px; /* Matching reference image radius */
          padding: 30px;
          box-shadow: 0 10px 40px rgba(0,0,0,0.2);
          display: flex;
          flex-direction: column;
          transform: translateY(20px);
          transition: transform 0.3s ease;
          overflow-y: auto; /* Scrollable content */
          position: relative;
      }
      
      .cart-modal-overlay.active .cart-modal-content {
          transform: translateY(0);
      }

      /* Scrollbar styling for the modal */
      .cart-modal-content::-webkit-scrollbar {
          width: 6px;
      }
      .cart-modal-content::-webkit-scrollbar-track {
          background: #f1f1f1; 
      }
      .cart-modal-content::-webkit-scrollbar-thumb {
          background: #ccc; 
          border-radius: 3px;
      }

      .cart-modal-header {
          display: flex;
          justify-content: center; /* Center the title */
          align-items: center;
          margin-bottom: 25px;
          position: relative;
      }
      .cart-modal-header h2 {
          font-size: 24px;
          color: #333;
          font-weight: 700;
          margin: 0;
      }
      .close-cart-btn {
          position: absolute;
          right: 0;
          top: -5px;
          font-size: 28px;
          cursor: pointer;
          border: none;
          background: none;
          color: #999;
          transition: color 0.2s;
          padding: 5px;
      }
      .close-cart-btn:hover {
          color: #333;
      }

      /* STEPPER STYLES - Compact for card */
      .stepper-wrapper {
          display: flex;
          justify-content: space-between;
          margin-bottom: 30px;
          position: relative;
          padding: 0 10px;
      }
      .stepper-wrapper::before {
          content: '';
          position: absolute;
          top: 15px;
          left: 30px;
          right: 30px;
          height: 2px;
          background: #eee;
          z-index: 1;
      }
      .stepper-item {
          position: relative;
          z-index: 2;
          display: flex;
          flex-direction: column;
          align-items: center;
          background: #fff; /* Mask line */
          padding: 0 5px;
      }
      .step-counter {
          width: 32px;
          height: 32px;
          border-radius: 50%;
          background: #fff;
          border: 2px solid #ddd;
          color: #999;
          display: flex;
          justify-content: center;
          align-items: center;
          font-weight: 600;
          margin-bottom: 5px;
          transition: all 0.3s;
          font-size: 14px;
      }
      .step-name {
          font-size: 11px;
          color: #bbb;
          font-weight: 600;
          text-transform: uppercase;
          letter-spacing: 0.5px;
      }
      .stepper-item.active .step-counter {
          border-color: #0d3625;
          color: #0d3625;
          background: #eafcf4;
      }
      .stepper-item.active .step-name {
          color: #0d3625;
      }
      .stepper-item.completed .step-counter {
          background: #0d3625;
          border-color: #0d3625;
          color: #fff;
      }
      .stepper-item.completed .step-name {
          color: #0d3625;
      }

      /* Content Area */
      .cart-body-wrapper {
          width: 100%;
      }
      .step-content {
          display: none;
          animation: fadeIn 0.4s ease;
      }
      .step-content.active {
          display: block;
      }
      @keyframes fadeIn {
          from { opacity: 0; transform: translateY(5px); }
          to { opacity: 1; transform: translateY(0); }
      }

      /* Step 1: Items List (Card Style) */
      .cart-items-section {
          background: transparent;
          padding: 0;
          box-shadow: none;
          border-radius: 0;
      }
      
      /* Simplify table for narrow view */
      .cart-table {
          width: 100%;
          border-collapse: collapse;
      }
      .cart-table thead {
          display: none; /* Hide headers for cleaner card look */
      }
      .cart-table tr {
          display: flex;
          flex-wrap: wrap;
          align-items: center;
          padding: 15px 0;
          border-bottom: 1px solid #eee;
      }
      .cart-table td {
          border: none;
          padding: 0;
          background: transparent;
      }
      .cart-table td:first-child { /* Product Name */
          width: 100%;
          font-weight: 600;
          color: #333;
          margin-bottom: 5px;
          font-size: 15px;
      }
      .cart-table td:nth-child(2) { /* Price */
          margin-right: 15px;
          color: #666;
          font-size: 14px;
      }
      .cart-table td:nth-child(3) { /* Qty */
          margin-right: auto;
          font-size: 14px;
          color: #666;
      }
      .cart-table td:nth-child(4) { /* Total */
          font-weight: bold;
          color: #0d3625;
          margin-right: 15px;
      }
      .cart-table td:last-child { /* Action */
          
      }
      .cart-table button {
          padding: 5px 10px;
          font-size: 12px;
          border-radius: 4px;
      }

      /* Summary Box (Embedded in flow) */
      .summary {
          width: 100%;
          background: #f9f9f9;
          padding: 20px;
          border-radius: 8px;
          margin-top: 20px;
          border: 1px solid #eee;
          box-shadow: none;
      }
      
      /* Form Inputs (Reference Image Style) */
      .form-container label {
          font-size: 13px;
          color: #666;
          margin-bottom: 6px;
          display: block;
          font-weight: 500;
      }
      .form-container input[type="text"], 
      .form-container textarea {
          width: 100%;
          padding: 12px;
          border: 1px solid #ddd;
          border-radius: 6px; /* Slightly squarer than before */
          font-size: 14px;
          color: #333;
          background: #fff;
          transition: border-color 0.2s;
          box-sizing: border-box; /* Critical for 100% width */
          margin-bottom: 5px;
      }
      .form-container input[type="text"]:focus,
      .form-container textarea:focus {
          border-color: #0d3625;
          outline: none;
      }
      
      /* Buttons */
      #checkoutBtn, #placeOrderBtn, #goToStep2Btn, #goToStep3Btn {
          width: 100%;
          background: #6c5ce7; /* Example blue/purple from reference? Or keep green */
          background: #4a90e2; /* Light blue like reference 'SIGN UP' */
          /* Let's stick to brand green but cleaner */
          background: #0d3625;
          color: #fff;
          padding: 14px;
          font-size: 15px;
          border-radius: 6px;
          border: none;
          font-weight: 600;
          cursor: pointer;
          margin-top: 20px;
          box-shadow: 0 4px 10px rgba(13, 54, 37, 0.2);
          transition: all 0.2s;
      }
      #checkoutBtn:hover, #placeOrderBtn:hover, #goToStep2Btn:hover, #goToStep3Btn:hover {
          background: #1a4d36;
          transform: translateY(-1px);
          box-shadow: 0 6px 12px rgba(13, 54, 37, 0.3);
      }
      
      #backToStep1Btn, #backToStep2Btn {
          width: 100%;
          background: #f1f2f6;
          color: #555;
          border: none;
          padding: 14px;
          border-radius: 6px;
          margin-top: 10px;
          font-weight: 600;
          cursor: pointer;
      }
      #backToStep1Btn:hover, #backToStep2Btn:hover {
          background: #e1e2e6;
      }

      /* Payment Options */
      .payment-options {
          display: flex;
          flex-direction: column; /* Stack them in narrow view */
          gap: 10px;
      }
      .payment-option label {
          text-align: left;
          padding: 15px;
          display: flex;
          align-items: center;
          gap: 10px;
      }
      .payment-option input {
          position: static;
          opacity: 1;
          width: auto;
          margin: 0;
      }
      
      /* Cart Actions (Buttons) */
      .cart-actions {
          display: flex;
          flex-direction: column-reverse; /* Stack: Back below Proceed */
          gap: 10px;
          margin-top: 25px;
      }
      .cart-actions button {
          width: 100%;
          margin: 0 !important; /* Override previous margins */
      }
      
      /* Empty State */
      #cartEmpty {
          text-align: center;
          padding: 60px;
          color: #999;
      }
    </style>
</head>
<body>
   
    <div class="navbar">
        <!-- Left Side: Logo + Greeting -->
        <div class="nav-left">
            <a href="index.php" class="logo"><img src="logo.jpeg" alt="logo"></a>
            <?php if($userName): ?>
                <span class="user-greeting">Hello, <strong><?php echo htmlspecialchars($userName); ?></strong></span>
            <?php endif; ?>
        </div>

        <!-- Right Side: Navigation + Actions -->
        <div class="nav-right">
            <a href="my_orders.php" class="nav-item">My Orders</a>
            <a href="admin_dashboard.php" class="nav-item">Admin</a>
            
            <?php if($userName): ?>
                <a href="logout.php" class="nav-item">Logout</a>
            <?php else: ?>
                <a href="register.php" class="nav-item">Register</a>
                <a href="login.php" class="nav-item">Login</a>
            <?php endif; ?>
            
            <button id="openCartBtn" class="nav-item">
                Cart 🛒
                <span id="cartCount" style="display:none;">0</span>
            </button>
        </div>
    </div>

    <div class="hero">
        <h1>Purity in Every Leaf.</h1>
        <p>Upto 40% OFF on Ayurvedic Essentials</p>
        <a href="#products" class="shop-now-btn">Shop Now</a>
    </div>

    <div class="shop-wrap">
        <div id="products" class="products-grid"></div>
        
        </div>

    <div id="cartModalOverlay" class="cart-modal-overlay">
        <div class="cart-modal-content">
            <div class="cart-modal-header">
                <h2>Shopping Cart</h2>
                <button class="close-cart-btn" id="closeCartBtn">×</button>
            </div>

            <!-- STEPPER -->
            <div class="stepper-wrapper">
                <div class="stepper-item active" data-step="1">
                    <div class="step-counter">1</div>
                    <div class="step-name">Order Summary</div>
                </div>
                <div class="stepper-item" data-step="2">
                    <div class="step-counter">2</div>
                    <div class="step-name">User Details</div>
                </div>
                <div class="stepper-item" data-step="3">
                    <div class="step-counter">3</div>
                    <div class="step-name">Payment</div>
                </div>
            </div>
            
            <div class="cart-body-wrapper">
                <!-- STEP 1: ORDER SUMMARY -->
                <div id="step1" class="step-content active">
                    <div class="cart-items-section">
                        <table class="cart-table">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="cartBody"></tbody>
                        </table>
                        <div id="cartEmpty" class="empty">Your cart is empty</div>
                    </div>
                    
                    <div id="billBox" class="summary" style="display:none; margin-top: 20px;">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <strong id="subAmt">₹0.00</strong>
                        </div>
                        <div class="summary-row">
                            <span>Tax (5%)</span>
                            <strong id="taxAmt">₹0.00</strong>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <strong id="shipAmt">₹0.00</strong>
                        </div>
                        <hr>
                        <div class="summary-row">
                            <strong>Total</strong>
                            <strong id="totalAmt">₹0.00</strong>
                        </div>
                        <button id="goToStep2Btn" class="btn" style="width:100%; margin-top:15px;">Proceed to Address</button>
                    </div>
                </div>

                <!-- STEP 2: USER DETAILS -->
                <div id="step2" class="step-content" style="display:none;">
                    <div class="form-container" style="max-width: 600px; margin: 0 auto;">
                        <h3 style="margin-bottom: 20px; color: #0d3625;">Shipping Details</h3>
                        <div style="margin-bottom:15px">
                            <label style="display:block; margin-bottom:5px; font-weight:600;">Full Name</label>
                            <input id="shipName" type="text" placeholder="Enter Full Name" value="<?php echo htmlspecialchars($profile['name'] ?? $userName ?? ''); ?>">
                        </div>
                        <div style="margin-bottom:15px">
                            <label style="display:block; margin-bottom:5px; font-weight:600;">Address</label>
                            <textarea id="shipAddress" placeholder="House No, Street, Area" rows="3"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                        </div>
                        <div style="display:grid;grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:15px">
                            <div>
                                <label style="display:block; margin-bottom:5px; font-weight:600;">City</label>
                                <input id="shipCity" type="text" placeholder="City" value="<?php echo htmlspecialchars($profile['city'] ?? ''); ?>">
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:5px; font-weight:600;">State</label>
                                <input id="shipState" type="text" placeholder="State" value="<?php echo htmlspecialchars($profile['state'] ?? ''); ?>">
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns: 1fr 1fr; gap:15px; margin-bottom:15px">
                            <div>
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Pincode</label>
                                <input id="shipPincode" type="text" placeholder="Pincode" value="<?php echo htmlspecialchars($profile['pincode'] ?? ''); ?>">
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Mobile Number</label>
                                <input id="shipMobile" type="text" placeholder="Mobile Number" value="<?php echo htmlspecialchars($profile['mobile'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <?php if ($userId): ?>
                        <div style="margin-bottom:20px;text-align:right">
                             <a href="profile.php" style="font-size:13px;color:#0d3625;text-decoration:none">Edit saved details →</a>
                        </div>
                        <?php endif; ?>

                        <div class="cart-actions">
                            <button id="backToStep1Btn" class="btn" style="background:#f1f2f6; color:#555;">Back</button>
                            <button id="goToStep3Btn" class="btn">Proceed to Payment</button>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: PAYMENT -->
                <div id="step3" class="step-content" style="display:none;">
                    <div class="payment-container" style="max-width: 500px; margin: 0 auto;">
                        <h3 style="margin-bottom: 20px; color: #0d3625;">Select Payment Method</h3>
                        
                        <div class="payment-options" style="flex-direction:column;">
                            <div class="payment-option" style="margin-bottom:10px;">
                                <input type="radio" name="payment_method" id="pay_online" value="online" checked>
                                <label for="pay_online" style="text-align:left; padding:15px; display:flex; justify-content:space-between; align-items:center;">
                                    <span>Pay Online (Secure)</span>
                                    <span style="font-size:12px; background:#eafcf4; padding:2px 6px; border-radius:4px; color:#0d3625;">Recommended</span>
                                </label>
                            </div>
                            <div style="margin-bottom:15px; padding-left:35px; color:#666; font-size:13px;">
                                Cards, UPI (GPay/PhonePe), Netbanking, Wallet
                            </div>

                            <div class="payment-option">
                                <input type="radio" name="payment_method" id="pay_cod" value="cod">
                                <label for="pay_cod" style="text-align:left; padding:15px;">Cash on Delivery</label>
                            </div>
                        </div>

                        <div class="summary" style="width:100%; margin-top:20px; background:#f9f9f9; border:none;">
                            <div class="summary-row">
                                <strong>Total Amount to Pay</strong>
                                <strong id="finalTotalAmt" style="font-size:1.2em; color:#0d3625;">₹0.00</strong>
                            </div>
                        </div>

                        <div class="cart-actions">
                            <button id="backToStep2Btn" class="btn" style="background:#f1f2f6; color:#555;">Back</button>
                            <button id="placeOrderBtn" class="btn">Place Order</button>
                        </div>
                        
                        <div id="orderStatus" style="margin-top:15px;color:#0d3625;background:#eafcf4;padding:10px;border-radius:8px;display:none;font-size:14px"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>


<script src="cart.js?v=<?php echo time(); ?>"></script>

<script>
    window.MS_IS_LOGGED = <?php echo $userId ? 'true' : 'false'; ?>;
    window.MS_USER_ID = <?php echo json_encode($userId); ?>;
    window.MS_USER = <?php echo json_encode($userName); ?>;
    
    // --- NEW LINE ADDED: Pass the pending ID to JavaScript ---
    window.MS_PENDING_ADD = <?php echo json_encode($pendingAddId); ?>;
    
    // NEW JAVASCRIPT FOR MODAL INTERACTION (This is correct)
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('cartModalOverlay');
        const openBtn = document.getElementById('openCartBtn');
        const closeBtn = document.getElementById('closeCartBtn');
        
        openBtn.addEventListener('click', () => {
            modal.classList.add('active');
        });

        closeBtn.addEventListener('click', () => {
            modal.classList.remove('active');
        });

        // Close when clicking outside the modal content
        modal.addEventListener('click', (e) => {
            if (e.target.id === 'cartModalOverlay') {
                modal.classList.remove('active');
            }
        });
        
        // This is where you might call a function from cart.js to update the count
        // For example: updateCartDisplay();
    });
</script>
