// cart.js
function renderCart() {
  const container = document.getElementById('cart-items');
  const totalEl = document.getElementById('cart-total');
  if (!container) return;
  // assume state is available via window.state from main.js
  container.innerHTML = '';
  let total = 0;
  for (const item of state.cart) {
    // fetch product details if needed; here we assume price is stored
    const row = document.createElement('div');
    row.className = 'cart-row';
    row.innerHTML = `
      <span class="name">${item.name}</span>
      <input type="number" min="1" value="${item.qty}" data-id="${item.id}" class="qty">
      <span class="price">₹${(item.qty * item.price).toFixed(2)}</span>
      <button class="remove" data-id="${item.id}">Remove</button>
    `;
    total += item.qty * item.price;
    container.appendChild(row);
  }
  if (totalEl) totalEl.textContent = `₹${total.toFixed(2)}`;
  saveState();
}
document.addEventListener('change', (e) => {
  if (e.target.classList.contains('qty')) {
    const id = e.target.dataset.id;
    const qty = parseInt(e.target.value, 10);
    const item = state.cart.find(i => i.id === id);
    if (item) item.qty = Math.max(1, qty);
    renderCart();
  }
});
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('remove')) {
    const id = e.target.dataset.id;
    state.cart = state.cart.filter(i => i.id !== id);
    renderCart();
  }
});
document.addEventListener('DOMContentLoaded', () => {
  renderCart();
});