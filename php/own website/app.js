// Simple storefront JS (no build step). Uses PHP API at /api if available, else localStorage.
(function(){
  const main = document.getElementById('main');
  const cartCount = document.getElementById('cartCount');
  const homeNav = document.getElementById('homeNav');
  const adminNav = document.getElementById('adminNav');
  const cartBtn = document.getElementById('cartBtn');

  let cart = JSON.parse(localStorage.getItem('nutra_leaf_cart')||'[]');
  let orders = [];

  function saveCart(){ localStorage.setItem('nutra_leaf_cart', JSON.stringify(cart)); renderCartCount(); }
  function renderCartCount(){ cartCount.textContent = cart.length; }

  function formatMoney(n){ return '₹'+n; }

  function renderHome(){
    main.innerHTML = '';
    const hero = document.createElement('div'); hero.className='hero';
    hero.innerHTML = `
      <div style="flex:1">
        <h1 style="font-size:28px;margin:0 0 8px">Elevate Your Daily Health</h1>
        <p class="muted">Pure, potent, and ethically sourced. We bring the best of nature to your doorstep.</p>
        <button class="btn" onclick="window.scrollTo({top:300,behavior:'smooth'})">Shop Collection</button>
      </div>
      <div style="width:300px;">
        <img src="https://images.unsplash.com/photo-1512036630240-199126773e73?auto=format&fit=crop&q=80&w=800" style="width:100%;border-radius:8px;" />
      </div>
    `;
    main.appendChild(hero);

    const grid = document.createElement('div'); grid.className='products';
    PRODUCTS.forEach(p=>{
      const card = document.createElement('div'); card.className='card';
      card.innerHTML = `
        <img src="${p.image}" alt="${p.name}" />
        <div class="body">
          <div class="flex space-between center"><div><strong>${p.name}</strong><div class="muted">${p.category}</div></div><div style="font-weight:800;color:var(--green)">${formatMoney(p.price)}</div></div>
          <p class="muted">${p.description}</p>
          <div style="margin:8px 0">${p.benefits.map(b=>`<span class="badge">${b}</span>`).join(' ')}</div>
          <button class="btn" data-id="${p.id}">Add to Cart</button>
        </div>
      `;
      grid.appendChild(card);
    });
    main.appendChild(grid);

    document.querySelectorAll('.btn[data-id]').forEach(b=>b.addEventListener('click',e=>{
      const id = e.currentTarget.getAttribute('data-id');
      const prod = PRODUCTS.find(x=>x.id===id);
      if(!prod) return;
      const existing = cart.find(i=>i.id===id);
      if(existing) existing.quantity++;
      else cart.push({...prod,quantity:1});
      saveCart();
      alert(prod.name+' added to cart');
    }));
  }

  function renderCheckout(){
    main.innerHTML = '';
    const wrapper = document.createElement('div');
    wrapper.innerHTML = `<h2>Your Cart</h2>`;
    const list = document.createElement('div');
    if(cart.length===0){ list.innerHTML = '<p class="muted">Cart is empty.</p>'; }
    else{
      cart.forEach(it=>{
        const row = document.createElement('div'); row.className='card'; row.style.marginBottom='8px';
        row.innerHTML = `<div style="display:flex;justify-content:space-between;align-items:center;padding:8px"><div><strong>${it.name}</strong><div class="muted">Qty: ${it.quantity}</div></div><div>${formatMoney(it.price*it.quantity)}</div></div>`;
        list.appendChild(row);
      });
      const total = cart.reduce((s,i)=>s+i.price*i.quantity,0);
      const foot = document.createElement('div'); foot.style.marginTop='12px'; foot.innerHTML = `<div class="flex space-between center"><strong>Total</strong><strong style="color:var(--green)">${formatMoney(total)}</strong></div><button class="btn" id="proceedBtn" style="margin-top:12px">Proceed to Checkout</button>`;
      list.appendChild(foot);
    }
    wrapper.appendChild(list);
    main.appendChild(wrapper);

    const proceed = document.getElementById('proceedBtn');
    if(proceed) proceed.addEventListener('click',()=>renderDetails());
  }

  function renderDetails(){
    main.innerHTML = `<h2>Shipping Details</h2>`;
    const form = document.createElement('div');
    form.innerHTML = `
      <div class="form-row"><input id="name" placeholder="Full name"/></div>
      <div class="form-row"><input id="phone" placeholder="Mobile number"/></div>
      <div class="form-row"><input id="city" placeholder="City"/></div>
      <div class="form-row"><textarea id="address" placeholder="Address"></textarea></div>
      <button class="btn" id="sendOtp">Send OTP</button>
    `;
    main.appendChild(form);
    document.getElementById('sendOtp').addEventListener('click',()=>{
      const phone = document.getElementById('phone').value.trim();
      if(phone.length!==10){ alert('Enter 10-digit number'); return; }
      alert('[SIMULATION] OTP sent: '+MOCK_OTP);
      renderOtp();
    });
  }

  function renderOtp(){
    main.innerHTML = `<h2>Enter OTP</h2><div class="form-row"><input id="otp" placeholder="123456"/></div><button class="btn" id="confirmOrder">Confirm Order</button>`;
    document.getElementById('confirmOrder').addEventListener('click',async ()=>{
      const val = document.getElementById('otp').value.trim();
      if(val!==MOCK_OTP){ alert('Invalid OTP'); return; }
      // build order
      const name = document.getElementById('name') ? document.getElementById('name').value : '';
      const phone = document.getElementById('phone') ? document.getElementById('phone').value : '';
      const city = document.getElementById('city') ? document.getElementById('city').value : '';
      const address = document.getElementById('address') ? document.getElementById('address').value : '';
      const total = cart.reduce((s,i)=>s+i.price*i.quantity,0);
      const order = { id:'ORD-'+Math.random().toString(36).substr(2,9).toUpperCase(), date:new Date().toLocaleString(), name, phone, city, address, items:cart, total, status:'Verified' };

      // try save to PHP API
      try{
        const resp = await fetch('api/save_order.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(order)});
        if(resp.ok){ const data = await resp.json(); console.log('Saved',data); }
        else throw new Error('API failed');
      }catch(e){
        // fallback to localStorage
        orders = JSON.parse(localStorage.getItem('nutra_leaf_orders')||'[]'); orders.unshift(order); localStorage.setItem('nutra_leaf_orders',JSON.stringify(orders));
      }

      cart = []; saveCart();
      main.innerHTML = `<h2>Order Placed!</h2><p class="muted">${order.id} — ${formatMoney(order.total)}</p><button class="btn" id="backHome">Return to Store</button>`;
      document.getElementById('backHome').addEventListener('click',()=>renderHome());
    });
  }

  async function renderAdmin(){
    main.innerHTML = `<h2>Admin — Orders</h2><div id="ordersWrap"></div><button class="btn" id="exportBtn" style="margin-top:12px">Export CSV</button>`;
    const wrap = document.getElementById('ordersWrap');
    // try fetch from API
    try{
      const resp = await fetch('api/list_orders.php');
      if(resp.ok){ orders = await resp.json(); }
      else throw new Error('no api');
    }catch(e){ orders = JSON.parse(localStorage.getItem('nutra_leaf_orders')||'[]'); }

    if(orders.length===0) wrap.innerHTML = '<p class="muted">No orders found.</p>';
    else{
      const tbl = document.createElement('table'); tbl.innerHTML = `<thead><tr><th>ID</th><th>Date</th><th>Customer</th><th>Phone</th><th>Amount</th><th>Status</th></tr></thead>`;
      const tbody = document.createElement('tbody'); orders.forEach(o=>{ const tr=document.createElement('tr'); tr.innerHTML=`<td class="muted">${o.id}</td><td>${o.date}</td><td>${o.name}</td><td>${o.phone}</td><td>${formatMoney(o.total)}</td><td>${o.status}</td>`; tbody.appendChild(tr); }); tbl.appendChild(tbody); wrap.appendChild(tbl);
    }

    document.getElementById('exportBtn').addEventListener('click',()=>{
      if(!orders || orders.length===0){ alert('No orders'); return; }
      const csv = [Object.keys(orders[0]).join(','), ...orders.map(o=>[o.id,o.date,`"${o.name}"`,o.phone,o.total,o.status].join(','))].join('\n');
      const blob = new Blob([csv],{type:'text/csv'}); const url = URL.createObjectURL(blob); const a=document.createElement('a'); a.href=url; a.download='orders.csv'; a.click(); URL.revokeObjectURL(url);
    });
  }

  // navigation
  homeNav.addEventListener('click',()=>renderHome());
  document.getElementById('homeBtn').addEventListener('click',()=>renderHome());
  adminNav.addEventListener('click',()=>renderAdmin());
  cartBtn.addEventListener('click',()=>renderCheckout());

  // init
  renderCartCount(); renderHome();
})();

// --- AI assistant UI (floating) ---
(function(){
  const body = document.body;
  const aiBtn = document.createElement('button');
  aiBtn.style.position='fixed'; aiBtn.style.right='20px'; aiBtn.style.bottom='20px'; aiBtn.style.width='64px'; aiBtn.style.height='64px'; aiBtn.style.borderRadius='50%'; aiBtn.style.background='var(--green)'; aiBtn.style.color='#fff'; aiBtn.style.border='0'; aiBtn.style.boxShadow='0 10px 30px rgba(2,6,23,.2)'; aiBtn.style.cursor='pointer'; aiBtn.textContent='AI';
  body.appendChild(aiBtn);

  const modal = document.createElement('div');
  modal.style.position='fixed'; modal.style.right='20px'; modal.style.bottom='100px'; modal.style.width='320px'; modal.style.maxWidth='90%'; modal.style.background='#fff'; modal.style.borderRadius='12px'; modal.style.boxShadow='0 20px 60px rgba(2,6,23,.25)'; modal.style.display='none'; modal.style.flexDirection='column'; modal.style.overflow='hidden';
  modal.innerHTML = `<div style="padding:12px;background:var(--green);color:#fff;font-weight:700;display:flex;justify-content:space-between;align-items:center">Nutra_leaf AI <button id="aiClose" style="background:transparent;border:0;color:#fff;cursor:pointer">✕</button></div><div style="padding:12px"><div id="aiResp" style="min-height:60px;color:#0f172a;margin-bottom:8px">Hi! Ask me which product fits your goal (sleep, stress, energy).</div><input id="aiInput" placeholder="Ex: Which one helps with sleep?" style="width:100%;padding:8px;border:1px solid #eef2f7;border-radius:8px;margin-bottom:8px"/><button id="aiSend" class="btn">Ask</button></div>`;
  body.appendChild(modal);

  aiBtn.addEventListener('click',()=>{ modal.style.display = modal.style.display === 'none' ? 'flex' : 'none'; });
  modal.querySelector('#aiClose').addEventListener('click',()=>{ modal.style.display='none'; });

  modal.querySelector('#aiSend').addEventListener('click', async ()=>{
    const input = modal.querySelector('#aiInput');
    const respEl = modal.querySelector('#aiResp');
    const q = input.value.trim();
    if(!q) return; respEl.textContent = 'Thinking...';
    try{
      const r = await fetch('api/ai_advice.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({query:q})});
      if(r.ok){ const j = await r.json(); respEl.textContent = j.advice || 'No advice.'; }
      else { respEl.textContent = 'AI service unavailable.'; }
    }catch(e){ respEl.textContent = 'AI request failed.'; }
  });
})();
