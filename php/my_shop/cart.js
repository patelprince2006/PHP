(function () {
  const PRODUCTS = [
    { id: 'p1', name: 'Classic Sneakers', price: 49.99, img: 'product.jpeg' },
    { id: 'p2', name: 'Leather Wallet', price: 24.99, img: 'product.jpeg' },
    { id: 'p3', name: 'Wireless Earbuds', price: 79.99, img: 'product.jpeg' }
  ];

  const TAX_RATE = 0.05;
  const SHIPPING_FEE = 5;
  const SHIPPING_FREE_OVER = 100;

  let cart = [];
  try {
    cart = JSON.parse(localStorage.getItem('ms_cart') || '[]');
    if (!Array.isArray(cart)) cart = [];
  } catch (e) {
    cart = [];
    console.error('Error loading cart:', e);
  }

  const productsEl = document.getElementById('products');
  const cartBody = document.getElementById('cartBody');
  const cartEmpty = document.getElementById('cartEmpty');
  const billBox = document.getElementById('billBox');

  const subAmt = document.getElementById('subAmt');
  const taxAmt = document.getElementById('taxAmt');
  const shipAmt = document.getElementById('shipAmt');
  const totalAmt = document.getElementById('totalAmt');
  const finalTotalAmt = document.getElementById('finalTotalAmt'); // New element in Step 3

  // STEP NAVIGATION BUTTONS
  const goToStep2Btn = document.getElementById('goToStep2Btn');
  const backToStep1Btn = document.getElementById('backToStep1Btn');
  const goToStep3Btn = document.getElementById('goToStep3Btn');
  const backToStep2Btn = document.getElementById('backToStep2Btn');
  
  // Payment & Order Elements
  const upiRow = document.getElementById('upiRow');
  const upiIdEl = document.getElementById('upiId');
  const placeOrderBtn = document.getElementById('placeOrderBtn');
  const orderStatus = document.getElementById('orderStatus');

  let currentStep = 1;

  function format(v) {
    return '₹' + v.toFixed(2);
  }

  /* PRODUCTS */
  function renderProducts() {
    if (!productsEl) return;
    productsEl.innerHTML = '';
    PRODUCTS.forEach(p => {
      productsEl.innerHTML += `
        <div class="product-card">
          <img src="${p.img}">
          <div class="card-body">
            <h3>${p.name}</h3>
            <p class="muted">${format(p.price)}</p>
            <button class="btn buy-btn" data-id="${p.id}">Buy</button>
          </div>
        </div>
      `;
    });

    document.querySelectorAll('.buy-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        const product = PRODUCTS.find(p => p.id === btn.dataset.id);
        if (window.MS_IS_LOGGED) {
          addToCart(product);   // user logged in — add normally
        } else {
          // not logged in: send user to login and preserve pending product id
          const url = new URL(window.location.href);
          const returnPage = 'index.php';
          window.location.href = `login.php?return=${encodeURIComponent(returnPage)}&pid=${encodeURIComponent(product.id)}`;
        }
      });
    });
  }

  function save() {
    localStorage.setItem('ms_cart', JSON.stringify(cart));
  }

  function addToCart(product) {
    const item = cart.find(i => i.id === product.id);
    if (item) item.qty++;
    else cart.push({ ...product, qty: 1 });
    save();
    
    // Ensure we are on Step 1 to see the cart
    showStep(1);
    renderCart();
    
    // Open cart modal if not already open
    const modal = document.getElementById('cartModalOverlay');
    if (modal && !modal.classList.contains('active')) {
        modal.classList.add('active');
    }
  }

  function renderCart() {
    // Update Cart Badge Count
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        const totalQty = cart.reduce((acc, item) => acc + (parseInt(item.qty) || 0), 0);
        cartCount.textContent = totalQty;
        cartCount.style.display = totalQty > 0 ? 'inline-block' : 'none';
        if (totalQty > 0) {
            cartCount.classList.remove('bump');
            void cartCount.offsetWidth; // trigger reflow
            cartCount.classList.add('bump');
        }
    }

    if (!cartBody) return;

    cartBody.innerHTML = '';

    if (cart.length === 0) {
      if (cartEmpty) cartEmpty.style.display = 'flex'; // Changed to flex for centering
      if (billBox) billBox.style.display = 'none';
      
      // Force back to step 1 if cart becomes empty
      if (typeof currentStep !== 'undefined' && currentStep !== 1) {
          showStep(1);
      }
      return;
    }

    if (cartEmpty) cartEmpty.style.display = 'none';
    if (billBox) billBox.style.display = 'block';

    let subtotal = 0;

    cart.forEach(item => {
      const total = item.price * item.qty;
      subtotal += total;

      cartBody.innerHTML += `
        <tr>
          <td>${item.name}</td>
          <td>${format(item.price)}</td>
          <td>${item.qty}</td>
          <td>${format(total)}</td>
          <td><button onclick="removeItem('${item.id}')">Remove</button></td>
        </tr>
      `;
    });

    const tax = subtotal * TAX_RATE;
    const shipping = subtotal >= SHIPPING_FREE_OVER ? 0 : SHIPPING_FEE;
    const grandTotal = subtotal + tax + shipping;

    if (subAmt) subAmt.textContent = format(subtotal);
    if (taxAmt) taxAmt.textContent = format(tax);
    if (shipAmt) shipAmt.textContent = format(shipping);
    if (totalAmt) totalAmt.textContent = format(grandTotal);
    
    // Update total in Step 3 as well
    if (finalTotalAmt) finalTotalAmt.textContent = format(grandTotal);
  }

  window.removeItem = function (id) {
    cart = cart.filter(i => i.id !== id);
    save();
    renderCart();
  }

  // --- STEPPER LOGIC ---

  function updateStepper(step) {
      document.querySelectorAll('.stepper-item').forEach(item => {
          const itemStep = parseInt(item.dataset.step);
          item.classList.remove('active', 'completed');
          
          if (itemStep === step) {
              item.classList.add('active');
          } else if (itemStep < step) {
              item.classList.add('completed');
          }
      });
  }

  function showStep(step) {
      // Hide all steps
      document.querySelectorAll('.step-content').forEach(el => el.style.display = 'none');
      document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));

      // Show target step
      const target = document.getElementById('step' + step);
      if (target) {
          target.style.display = 'block';
          // small timeout for fade effect if we added transition class
          setTimeout(() => target.classList.add('active'), 10);
      }
      
      updateStepper(step);
      currentStep = step;
  }

  // Step 1 -> Step 2
  if (goToStep2Btn) {
      goToStep2Btn.addEventListener('click', () => {
          if (cart.length === 0) {
              alert("Your cart is empty!");
              return;
          }
          showStep(2);
      });
  }

  // Step 2 -> Step 1
  if (backToStep1Btn) {
      backToStep1Btn.addEventListener('click', () => {
          showStep(1);
      });
  }

  // Step 2 -> Step 3
  if (goToStep3Btn) {
      goToStep3Btn.addEventListener('click', () => {
          // Validate Shipping Details
          const shipName = document.getElementById('shipName').value.trim();
          const shipAddress = document.getElementById('shipAddress').value.trim();
          const shipMobile = document.getElementById('shipMobile').value.trim();
          const shipCity = document.getElementById('shipCity').value.trim();
          const shipState = document.getElementById('shipState').value.trim();
          const shipPincode = document.getElementById('shipPincode').value.trim();

          if (!shipName || !shipAddress || !shipMobile || !shipCity || !shipState || !shipPincode) {
              alert("Please fill in all shipping details.");
              return;
          }
          
          // Simple mobile validation
          if (shipMobile.length < 10) {
              alert("Please enter a valid mobile number.");
              return;
          }

          showStep(3);
      });
  }

  // Step 3 -> Step 2
  if (backToStep2Btn) {
      backToStep2Btn.addEventListener('click', () => {
          showStep(2);
      });
  }

  // Payment Selection Logic
  // (No specific UI logic needed for radio buttons anymore as we removed the dynamic UPI input row)

  // PLACE ORDER LOGIC
  if (placeOrderBtn) {
      placeOrderBtn.addEventListener('click', async () => {
        const methodEl = document.querySelector('input[name="payment_method"]:checked');
        if (!methodEl) {
            alert("Please select a payment method");
            return;
        }
        const method = methodEl.value;
        
        // Re-collect shipping fields
        const shipName = (document.getElementById('shipName') || {}).value || '';
        const shipAddress = (document.getElementById('shipAddress') || {}).value || '';
        const shipCity = (document.getElementById('shipCity') || {}).value || '';
        const shipState = (document.getElementById('shipState') || {}).value || '';
        const shipPincode = (document.getElementById('shipPincode') || {}).value || '';
        const shipMobile = (document.getElementById('shipMobile') || {}).value || '';

        // Prepare base payload
        const payload = {
          cart: cart,
          total: totalAmt ? totalAmt.textContent : null,
          payment_method: method,
          user_id: (typeof window.MS_USER_ID !== 'undefined') ? window.MS_USER_ID : null,
          user_name: (typeof window.MS_USER !== 'undefined') ? window.MS_USER : null,
          shipping: {
            name: shipName.trim(),
            address: shipAddress.trim(),
            city: shipCity.trim(),
            state: shipState.trim(),
            pincode: shipPincode.trim(),
            mobile: shipMobile.trim()
          }
        };

        if (method === 'online') {
            // Calculate exact amount again for security
            const sub = cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
            const tax = sub * TAX_RATE;
            const ship = sub > SHIPPING_FREE_OVER ? 0 : SHIPPING_FEE;
            const finalAmount = sub + tax + ship;

            const options = {
                "key": "rzp_test_YOUR_KEY_HERE", // REPLACE WITH YOUR ACTUAL KEY
                "amount": Math.round(finalAmount * 100), // Amount in paise
                "currency": "INR",
                "name": "Nutra-Leaf Wellness",
                "description": "Order Payment",
                "image": "logo.jpeg",
                "handler": function (response) {
                    // Payment Success! Add payment ID to payload
                    payload.payment_id = response.razorpay_payment_id;
                    payload.status = 'Paid'; // Mark as paid
                    placeOrderOnServer(payload);
                },
                "prefill": {
                    "name": shipName,
                    "contact": shipMobile
                },
                "theme": {
                    "color": "#0d3625"
                },
                "modal": {
                    "ondismiss": function() {
                        alert('Payment cancelled');
                    }
                }
            };
            
            const rzp1 = new Razorpay(options);
            rzp1.open();
            
        } else {
            // Cash on Delivery
            placeOrderOnServer(payload);
        }

        function showOrderMessage(msg, isError){
          if (orderStatus) {
              orderStatus.style.color = isError ? 'red' : 'green';
              orderStatus.textContent = msg;
              orderStatus.style.display = 'block';
          } else {
              alert(msg);
          }
        }

        async function placeOrderOnServer(finalPayload) {
            placeOrderBtn.disabled = true;
            placeOrderBtn.textContent = 'Placing...';

            try {
              const res = await fetch('checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(finalPayload)
              });

              const text = await res.text();
              let data;
              try {
                data = text ? JSON.parse(text) : null;
              } catch (parseErr) {
                throw new Error(text || 'Server returned invalid response');
              }

              if (data && data.success) {
                // Clear cart
                cart = [];
                save();
                renderCart();
                
                // Alert user
                alert("Order Successfully Placed!");
                
                // Redirect
                window.location.href = 'order_confirmation.php?id=' + encodeURIComponent(data.order_id) + '&success=1';
              } else {
                throw new Error(data && data.error ? data.error : 'Unknown error');
              }
            } catch (e) {
              showOrderMessage('Order failed: ' + e.message, true);
            } finally {
              placeOrderBtn.disabled = false;
              placeOrderBtn.textContent = 'Place Order';
            }
        }
      });
  }

  // Initialize
  renderProducts();
  renderCart();
  
  // Reset to Step 1 on load
  showStep(1);

  // Handle post-login add
  (function handlePostLoginAdd() {
    const params = new URLSearchParams(window.location.search);
    const addId = params.get('add');
    if (addId) {
      const product = PRODUCTS.find(p => p.id === addId);
      if (product) {
        addToCart(product);
      }
      params.delete('add');
      const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
      history.replaceState(null, '', newUrl);
    }
  })();

  // Attach to static CTAs
  document.querySelectorAll('.cta').forEach(btn => {
    btn.addEventListener('click', () => {
      const name = btn.dataset.name;
      const product = PRODUCTS.find(p => p.name === name);
      if (!product) return;
      if (window.MS_IS_LOGGED) addToCart(product);
      else {
        const returnPage = 'index.php';
        window.location.href = `login.php?return=${encodeURIComponent(returnPage)}&pid=${encodeURIComponent(product.id)}`;
      }
    });
  });

})();
