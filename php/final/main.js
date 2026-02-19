// main.js
// Simple global state
const state = {
  cart: [],
  user: null
};

function saveState() {
  localStorage.setItem('flipclone_state', JSON.stringify(state));
}
function loadState() {
  const s = localStorage.getItem('flipclone_state');
  if (s) Object.assign(state, JSON.parse(s));
}
function addToCart(product, qty = 1) {
  const existing = state.cart.find(i => i.id === product.id);
  if (existing) existing.qty += qty;
  else state.cart.push({ id: product.id, name: product.name, price: product.price, qty });
  saveState();
  renderCartCount();
}
function renderCartCount() {
  const badge = document.getElementById('cart-count');
  if (badge) badge.textContent = state.cart.reduce((a, b) => a + b.qty, 0);
}
document.addEventListener('DOMContentLoaded', () => {
  loadState();
  renderCartCount();
  // Hook up search (assumes input with id="search")
  const searchInput = document.getElementById('search');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const q = e.target.value.toLowerCase();
      // Implement product filtering on the page (see product.js)
      window.dispatchEvent(new CustomEvent('search', { detail: q }));
    });
  }
});