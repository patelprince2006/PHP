document.addEventListener('DOMContentLoaded', function () {

  // --- Utility Functions: Cart Management ---
  function getCart() {
    try { return JSON.parse(localStorage.getItem('ms_cart') || '[]'); }
    catch(e){ return []; }
  }

  function saveCart(cart){ localStorage.setItem('ms_cart', JSON.stringify(cart)); }

  // Function to add a product to the cart (called by click handlers and the post-login logic)
  function addProductToCart(prod){
    const cart = getCart();
    // Use the reliable 'id' property for finding existing items
    const existing = cart.find(i => i.id === prod.id); 
    if (existing) existing.qty = (existing.qty||0) + (prod.qty||1);
    else cart.push(Object.assign({ qty: 1 }, prod));
    saveCart(cart);
    
    // You MUST call your cart rendering functions here for the user to see the change
    // If you have a function to redraw the table/summary, call it here, e.g.:
    // renderCart(cart); 
  }

  // Utility function for notifications (based on your code)
  function showToast(msg){
    let t = document.getElementById('msToast');
    if(!t){
      t = document.createElement('div'); t.id = 'msToast';
      t.style.position = 'fixed'; t.style.right = '16px'; t.style.bottom = '16px';
      t.style.background = '#222'; t.style.color = '#fff'; t.style.padding = '10px 14px';
      t.style.borderRadius = '8px'; t.style.boxShadow = '0 6px 18px rgba(0,0,0,0.12)';
      t.style.zIndex = '9999'; t.style.opacity = '0'; t.style.transition = 'opacity .18s ease';
      document.body.appendChild(t);
    }
    t.textContent = msg;
    t.style.opacity = '1';
    setTimeout(()=>{ t.style.opacity = '0'; }, 2000);
  }


  // --- Event Handler: Product "Buy" Click (Consolidated and Corrected) ---
  document.querySelectorAll('.cta').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      
      // Extract product data from HTML attributes
      const name = btn.dataset.name || btn.getAttribute('data-name') || 'Product';
      const price = parseFloat(btn.dataset.price || btn.getAttribute('data-price') || '0') || 0;
      let img = '';
      const item = btn.closest('.item') || btn.closest('.product-card');
      if(item){
        const imgel = item.querySelector('img');
        if(imgel) img = imgel.src || '';
      }
      
      // The ID must be consistent for local storage and for the login redirect
      const product = { 
        id: name.replace(/\s+/g,'_').toLowerCase(), 
        name: name, 
        price: price, 
        img: img 
      };

      if (window.MS_IS_LOGGED) {
        addProductToCart(product);
        showToast('Added to cart');
      } else {
        const returnPage = 'index.php';
        // Redirect to login, carrying the product's unique ID
        window.location.href = `login.php?return=${encodeURIComponent(returnPage)}&pid=${encodeURIComponent(product.id)}`;
      }
    });
  });


  // --- FIX FOR RE-LOGIN LOOP: Process Pending Add After Login ---
  if (window.MS_PENDING_ADD) {
    
    // IMPORTANT: This assumes the global PRODUCTS array is available.
    if (typeof PRODUCTS !== 'undefined') {
        // Find the full product object using the ID passed from PHP
        const product = PRODUCTS.find(p => p.id === window.MS_PENDING_ADD);

        if (product) {
          addProductToCart(product);
          showToast('Product added to cart automatically!');
          
          // Crucial: Clean the URL to prevent the add from happening on every refresh
          if (window.history.replaceState) {
              const cleanUrl = window.location.href.split('?')[0];
              window.history.replaceState({path:cleanUrl},'',cleanUrl);
          }
        }
    } else {
        // Fallback for when PRODUCTS array isn't defined on the page
        console.warn('Cannot complete pending add: Global PRODUCTS array is not defined.');
    }
  }
  // --- End Fix Logic ---
  
  // NOTE: Your other cart rendering, deletion, and checkout functions go here.

}); // closes document.addEventListener('DOMContentLoaded', ...