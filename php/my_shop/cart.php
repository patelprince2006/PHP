<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>cart</title>
  <link rel="stylesheet" href="cart.css">
</head>
<body>
  
</body>
</html>
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

  <div id="billBox" class="summary" style="display:none">
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

    <button id="checkoutBtn" class="btn">Checkout</button>
    <div id="paymentBox" style="display:none;margin-top:12px">
      <div style="margin-bottom:8px">Select payment method:</div>
      <label><input type="radio" name="payment_method" value="upi" checked> UPI</label>
      <label style="margin-left:12px"><input type="radio" name="payment_method" value="cod"> Cash on Delivery</label>

      <div id="upiRow" style="margin-top:8px">
        <input id="upiId" type="text" placeholder="Enter UPI ID (example@bank)" style="padding:8px;width:100%;max-width:320px">
      </div>

      <div id="shippingBox" style="margin-top:12px">
        <div style="font-weight:600;margin-bottom:8px">Shipping details</div>
        <div style="margin-bottom:8px">
          <input id="shipName" type="text" placeholder="Full name" style="padding:8px;width:100%;max-width:420px" value="<?php echo htmlspecialchars($profile['name'] ?? $userName ?? ''); ?>">
        </div>
        <div style="margin-bottom:8px">
          <textarea id="shipAddress" placeholder="Address (street, area, landmark)" style="padding:8px;width:100%;max-width:420px" rows="3"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
        </div>
        <div style="display:flex;gap:8px;margin-bottom:8px;flex-wrap:wrap">
          <input id="shipCity" type="text" placeholder="City" style="padding:8px;width:160px" value="<?php echo htmlspecialchars($profile['city'] ?? ''); ?>">
          <input id="shipState" type="text" placeholder="State" style="padding:8px;width:160px" value="<?php echo htmlspecialchars($profile['state'] ?? ''); ?>">
          <input id="shipPincode" type="text" placeholder="Pincode" style="padding:8px;width:120px" value="<?php echo htmlspecialchars($profile['pincode'] ?? ''); ?>">
        </div>
        <div style="margin-bottom:8px">
          <input id="shipMobile" type="text" placeholder="Mobile number" style="padding:8px;width:200px" value="<?php echo htmlspecialchars($profile['mobile'] ?? ''); ?>">
        </div>
        <div style="margin-bottom:8px">
          <?php if ($userId): ?>
            <a href="profile.php" style="font-size:13px">Edit saved address</a>
          <?php endif; ?>
        </div>
      </div>

      <div style="margin-top:10px">
        <button id="placeOrderBtn" class="btn">Place Order</button>
        <button id="cancelPaymentBtn" class="btn" style="background:#e6e9eb;margin-left:8px">Cancel</button>
      </div>

      <div id="orderStatus" style="margin-top:10px;color:green;display:none"></div>
    </div>
  </div>

</div>

<script src="cart.js"></script>

<script>
    window.MS_IS_LOGGED = <?php echo $userId ? 'true' : 'false'; ?>;
    window.MS_USER_ID = <?php echo json_encode($userId); ?>;
    window.MS_USER = <?php echo json_encode($userName); ?>;
</script>